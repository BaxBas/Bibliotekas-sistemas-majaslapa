<?php
    header("Content-Type: application/json; charset=UTF-8");
    require_once '../../database/db_connect.php';
    require_once 'audit-helper.php';

    if ($conn->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Datubāzes pieslēguma kļūda!"]);
        exit;
    }

    session_start();
    if (!isset($_SESSION['loggedin'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(["error" => "Nav autorizācijas!"]);
        exit;
    }

    // Palīgfunkcija datu validācijai:
    function validet_laukus($dati) {
        $obligati_lauki = ['nosaukums', 'apraksts', 'autors', 'statuss'];
        
        foreach ($obligati_lauki as $lauks) {
            if (!isset($dati[$lauks]) || trim($dati[$lauks]) === '') {
                http_response_code(400); // Bad Request
                echo json_encode(["error" => "Lauks '$lauks' ir obligāts un nevar būt tukšs"]);
                exit;
            }
        }
    }

    $metode = $_SERVER['REQUEST_METHOD'];
    $limits = 5;

    // Iegūst visas gramatas
    if ($metode === 'GET') {
        $search_input = trim($_GET['search'] ?? '');
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $sql = $conn->prepare("SELECT * FROM gramatas WHERE id=?");
            $sql->bind_param("i", $id);
            $sql->execute();
            $rezultats = $sql->get_result();

            if ($gramata = $rezultats->fetch_assoc()) {
                echo json_encode($gramata);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["error" => "Grāmata nav atrasta"]);
            }
            $sql->close();
            exit;
        }

        $lapa = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($lapa - 1) * $limits;
        $statuss = "dzēsts";
        
        if (!empty($search_input)) {
            $search = "%" . $search_input . "%";

            // saskaita, cik kopā atbilst meklēšanai
            $kopaSql = $conn->prepare("
                SELECT COUNT(*) AS kopa
                FROM gramatas
                WHERE (nosaukums LIKE ? OR autors LIKE ?) AND statuss != ?
            ");
            $kopaSql->bind_param("sss", $search, $search, $statuss);
            $kopaSql->execute();
            $kopa = (int)$kopaSql->get_result()->fetch_assoc()['kopa'];
            $kopaSql->close();

            $sql = $conn->prepare("
                SELECT * FROM gramatas 
                WHERE (nosaukums LIKE ? OR autors LIKE ?) AND statuss != ?
                ORDER BY id DESC
                LIMIT ? OFFSET ?
            ");
            $sql->bind_param("sssii", $search, $search, $statuss, $limits, $offset);
            $sql->execute();
            $rez = $sql->get_result();

            // Reģistrē meklēšanu
            $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
            pievienotLogu($conn, $darbinieks, 'Meklēts', 'Grāmatas', $search_input);
        } else {
            // saskaita, cik ir grāmatu
            $kopaSql = $conn->prepare("
                SELECT COUNT(*) AS kopa
                FROM gramatas
                WHERE statuss != ?
            ");
            $kopaSql->bind_param("s", $statuss);
            $kopaSql->execute();
            $kopa = (int)$kopaSql->get_result()->fetch_assoc()['kopa'];
            $kopaSql->close();

            $sql = $conn->prepare("SELECT * FROM gramatas WHERE statuss != ? ORDER BY id DESC LIMIT ? OFFSET ?");
            $sql->bind_param("sii", $statuss, $limits, $offset);
            $sql->execute();
            $rez = $sql->get_result();
        }

        $gramatas = [];
        while ($row = $rez->fetch_assoc()) {
            $gramatas[] = $row;
        }
        $sql->close();

        echo json_encode([
            "dati" => $gramatas,
            "kopa" => $kopa,
            "limits" => $limits
        ]);
        exit;
    }

    elseif ($metode === 'POST'){
        validet_laukus($_POST);
        $nosaukums = $_POST['nosaukums'];
        $apraksts = $_POST['apraksts'];
        $autors = $_POST['autors']; 
        $statuss = $_POST['statuss']; 
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;

        $jauna_bilde = null;
        if (isset($_FILES['bilde']) && $_FILES['bilde']['error'] === UPLOAD_ERR_OK) {
            $fails = $_FILES['bilde'];
            $atlauts_tips = ['image/jpeg', 'image/png'];
            $max_izmers = 2 * 1024 * 1024; // 2MB

            // tips
            if (!in_array($fails['type'], $atlauts_tips)) {
                http_response_code(400);
                echo json_encode(["error" => "Atļauti tikai .jpg un .png faili"]);
                exit;
            }

            if ($fails['size'] > $max_izmers) {
                http_response_code(400);
                echo json_encode(["error" => "Pārāk liels attēla fails (max. 2MB)"]);
                exit;
            }

            $jauna_bilde = uniqid() . "_" . basename($fails['name']);
            $galamerkis = "../../img/" . $jauna_bilde;

            if (!move_uploaded_file($fails['tmp_name'], $galamerkis)) {
                http_response_code(500);
                echo json_encode(["error" => "Neizdevās augšupielādēt attēlu"]);
                exit;
            }
        }

        if($id) {
            // --------------------------------------------
            // Iegūst vecos datus
            $vecais_sql = $conn->prepare("SELECT * FROM gramatas WHERE id = ?");
            $vecais_sql->bind_param("i", $id);
            $vecais_sql->execute();
            $vecie_dati = $vecais_sql->get_result()->fetch_assoc();
            $vecais_sql->close();

            if (!$vecie_dati) {
                http_response_code(404);
                echo json_encode(["error" => "Grāmata nav atrasta!"]);
                exit;
            }

            // skatās uz izmaiņām
            $izmainas = [];
            if ($vecie_dati['nosaukums'] !== $nosaukums) {
                $izmainas[] = "Nosaukums: {$vecie_dati['nosaukums']} -> $nosaukums";
            }
            if ($vecie_dati['apraksts'] !== $apraksts) {
                $izmainas[] = "Apraksts: {$vecie_dati['apraksts']} -> $apraksts";
            }
            if ($vecie_dati['autors'] !== $autors) {
                $izmainas[] = "Autors: {$vecie_dati['autors']} -> $autors";
            }
            if ($vecie_dati['statuss'] !== $statuss) {
                $izmainas[] = "Statuss: {$vecie_dati['statuss']} -> $statuss";
            }

            if ($jauna_bilde !== null) {
                $izmainas[] = "Bilde: Mainīta";
            }
// --------------------------------------------
            if ($jauna_bilde) {
                // updato visu un ari bildi
                $sql = $conn->prepare("UPDATE gramatas SET nosaukums=?, apraksts=?, autors=?, statuss=?, bilde=? WHERE id=?");
                $sql->bind_param("sssssi", $nosaukums, $apraksts, $autors, $statuss, $jauna_bilde, $id);
            } else {
                // updateo visu iznemot bildi
                $sql = $conn->prepare("UPDATE gramatas SET nosaukums=?, apraksts=?, autors=?, statuss=? WHERE id=?");
                $sql->bind_param("ssssi", $nosaukums, $apraksts, $autors, $statuss, $id);
            }

            if ($sql->execute()) {
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
// --------------------------------------------
                if (!empty($izmainas)) {
                    $apraksts = "Grāmata #$id:\n " . implode(",\n ", $izmainas);
                    pievienotLogu($conn, $darbinieks, 'Rediģēts', 'Grāmatas', $apraksts);
                }
// --------------------------------------------
                echo json_encode(["message" => "Grāmata veiksmīgi atjaunināta!"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Kļūda atjauninot datus!"]);
            }
        } else {
            // New record - fallback to default image if none uploaded
            $bilde_nosaukums = $jauna_bilde ?? "default.png";
    
            // Glabā bāzē
            $sql = $conn->prepare("INSERT INTO gramatas (nosaukums, apraksts, autors, statuss, bilde) VALUES (?, ?, ?, ?, ?)");
            $sql->bind_param("sssss", $nosaukums, $apraksts, $autors, $statuss, $bilde_nosaukums);

            if ($sql->execute()) {   
                // izveido ierakstu audit žurnālā
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
                pievienotLogu($conn, $darbinieks, 'Pievienots', 'Grāmatas', $nosaukums);

                http_response_code(201);
                echo json_encode(["message" => "Grāmata veiksmīgi pievienota!"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Kļūda saglabājot datubāzē!"]);
            }
        }
        $sql->close();
        exit;
    }elseif ($metode === 'DELETE') {
        if (!isset($_GET['id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Nepieciešams ID"]);
            exit;
        }

        $statuss = "dzēsts";

        // Iegust nosaukumu, ko dzest
        $id = (int) $_GET['id'];
        $sql = $conn->prepare("SELECT nosaukums FROM gramatas WHERE id = ? AND statuss!=?");
        $sql->bind_param("is", $id, $statuss);
        $sql->execute();
        $item_res = $sql->get_result()->fetch_assoc();
        $nosaukums = $item_res['nosaukums'] ?? "ID #$id";
        $sql->close();

        // soft delete
        $sql = $conn->prepare("UPDATE gramatas SET statuss='dzēsts' WHERE id=? AND statuss!=?");
        $sql->bind_param("is", $id, $statuss);

        if (!$sql->execute()) {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Neizdevās dzēst grāmatu"]);
            exit;
        }

        if ($sql->affected_rows === 0) {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "Grāmata nav atrasta"]);
            exit;
        }
        // izveido ierakstu audit žurnālā
        $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
        pievienotLogu($conn, $darbinieks, 'Dzēsts', 'Grāmatas', $nosaukums);
        
        echo json_encode(["message" => "Grāmata veiksmīgi dzēsta"]);
        $sql->close();
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Metode nav atbalstīta"]);
    }

    $conn->close();
?>