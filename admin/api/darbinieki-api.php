<?php
    header("Content-Type: application/json; charset=UTF-8");

    // require_once '../../database/db_connect.php';
    require_once 'audit-helper.php';

    if ($conn->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Neizdevās pieslēgties datubāzei"]); 
        exit;
    }

    session_start();
    if (!isset($_SESSION['loggedin'])) {
        http_response_code(401);
        echo json_encode(["error" => "Nav autorizācijas!"]);
        exit;
    }

    function validet_laukus($lietotajvards, $loma) {
        if (empty($lietotajvards) || empty($loma)) {
            http_response_code(400);
            echo json_encode(["error" => "Visi lauki ir obligāti!"]);
            exit;
        }

        if (mb_strlen($lietotajvards) > 40) {
            http_response_code(400);
            echo json_encode(["error" => "Lietotājvārds nevar pārsniegt 40 rakstzīmes!"]);
            exit;
        }

        if ($loma !== 'administrators' && $loma !== 'moders') {
            http_response_code(400);
            echo json_encode(["error" => "Norādīta nepareiza loma!"]);
            exit;
        }
    }

    // $obligata = true  -> parole jānorāda (jauns darbinieks)
    // $obligata = false -> parole var būt tukša (rediģēšana, paroli nemaina)
    function validet_paroli($parole, $obligata) {
        if ($parole === '') {
            if ($obligata) {
                http_response_code(400);
                echo json_encode(["error" => "Parole ir obligāta!"]);
                exit;
            }
            return;
        }

        if (mb_strlen($parole) < 8) {
            http_response_code(400);
            echo json_encode(["error" => "Parolei jābūt vismaz 8 rakstzīmēm!"]);
            exit;
        }

        if (strlen($parole) > 72) { // bcrypt ignorē visu pēc 72 baitiem
            http_response_code(400);
            echo json_encode(["error" => "Parole nevar pārsniegt 72 baitus!"]);
            exit;
        }
    }

    $metode = $_SERVER['REQUEST_METHOD'];

    if ($metode === 'GET') {
        $search_input = trim($_GET['search'] ?? '');
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $sql = $conn->prepare("SELECT id, lietotajvards, loma, statuss FROM darbinieki WHERE id=?");
            $sql->bind_param("i", $id);
            $sql->execute();
            $rezultats = $sql->get_result();

            if ($darbin = $rezultats->fetch_assoc()) {
                echo json_encode($darbin);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["error" => "Darbinieks nav atrasts"]);
            }
            $sql->close();
            exit;
        }

        $limits = 10;
        $lapa = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($lapa - 1) * $limits;
        $statuss = "aktīvs";
        
        if (!empty($search_input)) {
            $search = "%" . $search_input . "%";

            // saskaita, cik kopā atbilst meklēšanai
            $kopaSql = $conn->prepare("
                SELECT COUNT(*) AS kopa
                FROM darbinieki
                WHERE lietotajvards LIKE ? AND statuss = ?
            ");
            $kopaSql->bind_param("ss", $search, $statuss);
            $kopaSql->execute();
            $kopa = (int)$kopaSql->get_result()->fetch_assoc()['kopa'];
            $kopaSql->close();

            $sql = $conn->prepare("
                SELECT id, lietotajvards, loma, statuss
                FROM darbinieki
                WHERE lietotajvards LIKE ? AND statuss = ?
                ORDER BY id DESC
                LIMIT ? OFFSET ?
            ");
            $sql->bind_param("ssii", $search, $statuss, $limits, $offset);
            $sql->execute();
            $rez = $sql->get_result();

            // Reģistrē meklēšanu
            $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
            pievienotLogu($conn, $darbinieks, 'Meklēts', 'Darbinieki', $search_input);
        } else {
            // saskaita, cik ir darbinieku kopā
            $kopaRez = $conn->query("SELECT COUNT(*) AS kopa FROM darbinieki WHERE statuss = 'aktīvs'");
            $kopa = (int)$kopaRez->fetch_assoc()['kopa'];

            $sql = $conn->prepare("SELECT id, lietotajvards, loma, statuss FROM darbinieki WHERE statuss = ? ORDER BY id DESC LIMIT ? OFFSET ?");
            $sql->bind_param("sii", $statuss, $limits, $offset);
            $sql->execute();
            $rez = $sql->get_result();
        }

        $darbinieki = [];
        while ($darbin = $rez->fetch_assoc()) {
            $darbinieki[] = $darbin;
        }

        echo json_encode([
            "dati" => $darbinieki,
            "kopa" => $kopa
        ]);

        if (isset($sql) && $sql) {
            $sql->close();
        }
    } elseif ($metode === 'POST') {
        $action = $_POST['action'] ?? '';
        $lietotajvards = trim($_POST['lietotajvards'] ?? '');
        $loma = trim($_POST['loma'] ?? '');
        $parole = $_POST['parole'] ?? ''; // netrimmo paroli

        validet_laukus($lietotajvards, $loma);
        validet_paroli($parole, $action !== 'update'); // obligāta tikai veidojot jaunu

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);

            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Nederīgs ID!"]);
                exit;
            }

            // Iegūst vecos datus
            $vecais_sql = $conn->prepare("SELECT lietotajvards, loma FROM darbinieki WHERE id = ?");
            $vecais_sql->bind_param("i", $id);
            $vecais_sql->execute();
            $vecie_dati = $vecais_sql->get_result()->fetch_assoc();
            $vecais_sql->close();

            if (!$vecie_dati) {
                http_response_code(404);
                echo json_encode(["error" => "Darbinieks nav atrasts!"]);
                exit;
            }

            $izmainas = [];
            if ($vecie_dati['lietotajvards'] !== $lietotajvards) {
                $izmainas[] = "Lietotājvārds: {$vecie_dati['lietotajvards']} -> $lietotajvards";
            }
            if ($vecie_dati['loma'] !== $loma) {
                $izmainas[] = "Loma: {$vecie_dati['loma']} -> $loma";
            }
            if ($parole !== '') {
                $izmainas[] = "Parole nomainīta"; // pašu paroli nekad neloģē
            }

            if ($parole !== '') {
                $hash = password_hash($parole, PASSWORD_DEFAULT);
                $sql = $conn->prepare("UPDATE darbinieki SET lietotajvards = ?, loma = ?, parole = ? WHERE id = ?");
                $sql->bind_param("sssi", $lietotajvards, $loma, $hash, $id);
            } else {
                $sql = $conn->prepare("UPDATE darbinieki SET lietotajvards = ?, loma = ? WHERE id = ?");
                $sql->bind_param("ssi", $lietotajvards, $loma, $id);
            }

            if ($sql->execute()) {
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
                if (!empty($izmainas)) {
                    $apraksts = "Darbinieks #$id:\n " . implode(",\n ", $izmainas);
                    pievienotLogu($conn, $darbinieks, 'Rediģēts', 'Darbinieki', $apraksts);
                }
                echo json_encode(["message" => "Darbinieks veiksmīgi rediģēts"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Neizdevās rediģēt darbinieku"]);
            }
            $sql->close();
            exit;
        } else {
            // Jauna darbinieka izveide
            $hash = password_hash($parole, PASSWORD_DEFAULT);
            $sql = $conn->prepare("INSERT INTO darbinieki (lietotajvards, parole, loma, statuss) VALUES (?, ?, ?, 'aktīvs')");
            $sql->bind_param("sss", $lietotajvards, $hash, $loma);

            if ($sql->execute()) {
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
                pievienotLogu($conn, $darbinieks, 'Pievienots', 'Darbinieki', "$lietotajvards ($loma)");

                http_response_code(201);
                echo json_encode(["message" => "Darbinieks veiksmīgi pievienots!"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Neizdevās pievienot darbinieku"]);
            }
            $sql->close();
            exit;
        }
    }
    elseif ($metode === 'DELETE') {
        if (!isset($_GET['id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Nepieciešams ID"]);
            exit;
        }

        $statuss = "dzēsts";

        // Iegūst lietotājvārdu, ko dzēst
        $id = (int) $_GET['id'];
        $sql = $conn->prepare("SELECT lietotajvards FROM darbinieki WHERE id = ? AND statuss != ?");
        $sql->bind_param("is", $id, $statuss);
        $sql->execute();
        $item_res = $sql->get_result()->fetch_assoc();
        $nosaukums = $item_res['lietotajvards'] ?? "ID #$id";
        $sql->close();

        // soft delete
        $sql = $conn->prepare("UPDATE darbinieki SET statuss = ? WHERE id = ? AND statuss != ?");
        $sql->bind_param("sis", $statuss, $id, $statuss);

        if (!$sql->execute()) {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Neizdevās dzēst darbinieku"]);
            exit;
        }

        if ($sql->affected_rows === 0) {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "Darbinieks nav atrasts"]);
            exit;
        }

        // izveido ierakstu audit žurnālā
        $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
        pievienotLogu($conn, $darbinieks, 'Dzēsts', 'Darbinieki', $nosaukums);
        
        echo json_encode(["message" => "Darbinieks veiksmīgi dzēsts"]);
        $sql->close();
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Metode nav atbalstīta"]);
    }

    $conn->close();
?>