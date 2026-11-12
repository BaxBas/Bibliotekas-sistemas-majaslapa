<?php
    header("Content-Type: application/json; charset=UTF-8");

    // require_once '../../database/db_connect.php';
    require_once 'audit-helper.php';
    require_once __DIR__ . '/../../vendor/autoload.php';

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

    function validet_laukus($vards, $uzvards, $epasts) {
        if (empty($vards) || empty($uzvards) || empty($epasts)) {
            http_response_code(400);
            echo json_encode(["error" => "Visi lauki ir obligāti!"]);
            exit;
        }

        if (mb_strlen($vards) > 60) {
            http_response_code(400);
            echo json_encode(["error" => "Vārds nevar pārsniegt 60 rakstzīmes!"]);
            exit;
        }

        if (mb_strlen($uzvards) > 60) {
            http_response_code(400);
            echo json_encode(["error" => "Uzvārds nevar pārsniegt 60 rakstzīmes!"]);
            exit;
        }

        if (mb_strlen($epasts) > 80) {
            http_response_code(400);
            echo json_encode(["error" => "E-pasts nevar pārsniegt 80 rakstzīmes!"]);
            exit;
        }

        if (!filter_var($epasts, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "Nepareiza e-pasta adrese!"]);
            exit;
        }

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $token = $_ENV['token'] ?? '';

        if (empty($token)) {
            error_log("Trūkst API Token");
            return; 
        }

        $url = "https://easyemailapi.com/api/verify/" . urlencode($epasts);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if ($response === false) {
            echo "Error: " . curl_error($ch);
            http_response_code(500);
            echo json_encode(["error" => "Neizdevās pārbaudīt e-pastu, jo API nav pieejams. Mēģiniet vēlāk!"]);
            exit;
        }
        curl_close($ch);
    
        $data = json_decode($response, true);
        $valid = false;

        if (isset($data['score']) && isset($data['max_score']) && $data['score'] >= 0.8 * $data['max_score']) {
            $valid = true;
        }

        if (!$valid) {
            http_response_code(400);
            echo json_encode(["error" => "E-pasts nav derīgs vai ir aizdomīgs!"]);
            exit;
        }
    }

    $metode = $_SERVER['REQUEST_METHOD'];

    if ($metode === 'GET') {
        $search_input = trim($_GET['search'] ?? '');
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $sql = $conn->prepare("SELECT * FROM lasitaji WHERE id=?");
            $sql->bind_param("i", $id);
            $sql->execute();
            $rezultats = $sql->get_result();

            if ($lasitajs = $rezultats->fetch_assoc()) {
                echo json_encode($lasitajs);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["error" => "Lasītājs nav atrasts"]);
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
                FROM lasitaji
                WHERE (vards LIKE ? OR uzvards LIKE ? OR epasts LIKE ?) AND statuss = ?
            ");
            $kopaSql->bind_param("ssss", $search, $search, $search, $statuss);
            $kopaSql->execute();
            $kopa = (int)$kopaSql->get_result()->fetch_assoc()['kopa'];
            $kopaSql->close();

            $sql = $conn->prepare("
                SELECT *
                FROM lasitaji 
                WHERE (vards LIKE ? OR uzvards LIKE ? OR epasts LIKE ?) AND statuss = ?
                ORDER BY id DESC
                LIMIT ? OFFSET ?
            ");
            $sql->bind_param("ssssii", $search, $search, $search, $statuss, $limits, $offset);
            $sql->execute();
            $rez = $sql->get_result();

            // Reģistrē meklēšanu
            $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
            pievienotLogu($conn, $darbinieks, 'Meklēts', 'Lasītāji', $search_input);
        } else {
            // saskaita, cik ir klientu kopā
            $kopaRez = $conn->query("SELECT COUNT(*) AS kopa FROM lasitaji WHERE statuss = 'aktīvs'");
            $kopa = (int)$kopaRez->fetch_assoc()['kopa'];

            $sql = $conn->prepare("SELECT * FROM lasitaji WHERE statuss = ? ORDER BY id DESC LIMIT ? OFFSET ?");
            $sql->bind_param("sii", $statuss, $limits, $offset);
            $sql->execute();
            $rez = $sql->get_result();
        }

        $lasitaji = [];
        while ($lasitajs = $rez->fetch_assoc()) {
            $lasitaji[] = $lasitajs;
        }

        echo json_encode([
            "dati" => $lasitaji,
            "kopa" => $kopa
        ]);

        if (isset($sql) && $sql) {
            $sql->close();
        }
    } elseif ($metode === 'POST') {
        $action = $_POST['action'] ?? '';
        $vards = trim($_POST['vards'] ?? '');
        $uzvards = trim($_POST['uzvards'] ?? '');
        $epasts = trim($_POST['epasts'] ?? '');

        validet_laukus($vards, $uzvards, $epasts);

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);

            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Nederīgs ID!"]);
                exit;
            }

            // Iegūst vecos datus
            $vecais_sql = $conn->prepare("SELECT vards, uzvards, epasts FROM lasitaji WHERE id = ?");
            $vecais_sql->bind_param("i", $id);
            $vecais_sql->execute();
            $vecie_dati = $vecais_sql->get_result()->fetch_assoc();
            $vecais_sql->close();

            if (!$vecie_dati) {
                http_response_code(404);
                echo json_encode(["error" => "Lasītājs nav atrasts!"]);
                exit;
            }

            $izmainas = [];
            if ($vecie_dati['vards'] !== $vards) {
                $izmainas[] = "Vārds: {$vecie_dati['vards']} -> $vards";
            }
            if ($vecie_dati['uzvards'] !== $uzvards) {
                $izmainas[] = "Uzvārds: {$vecie_dati['uzvards']} -> $uzvards";
            }
            if ($vecie_dati['epasts'] !== $epasts) {
                $izmainas[] = "Epasts: {$vecie_dati['epasts']} -> $epasts";
            }

            $sql = $conn->prepare("UPDATE lasitaji SET vards = ?, uzvards = ?, epasts = ? WHERE id = ?");
            $sql->bind_param("sssi", $vards, $uzvards, $epasts, $id);

            if ($sql->execute()) {
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
                if (!empty($izmainas)) {
                    $apraksts = "Lasītājs #$id:\n " . implode(",\n ", $izmainas);
                    pievienotLogu($conn, $darbinieks, 'Rediģēts', 'Lasītāji', $apraksts);
                }
                echo json_encode(["message" => "Lasītājs veiksmīgi rediģēts"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Neizdevās rediģēt lasītāju"]);
            }
            $sql->close();
            exit;
        } else {
            // New reader creation branch
            $sql = $conn->prepare("INSERT INTO lasitaji (vards, uzvards, epasts, statuss) VALUES (?, ?, ?, 'aktīvs')");
            $sql->bind_param("sss", $vards, $uzvards, $epasts);

            if ($sql->execute()) {
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
                pievienotLogu($conn, $darbinieks, 'Pievienots', 'Lasītāji', "$vards $uzvards");

                http_response_code(201);
                echo json_encode(["message" => "Lasītājs veiksmīgi pievienots!"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Neizdevās pievienot lasītāju"]);
            }
            $sql->close();
            exit;
        }
    }
    elseif ($metode === 'DELETE') {
        $id = (int)($_GET['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => false, 'message' => 'Derīgs ID nav norādīts!']);
            exit();
        }
        // Meklē lasītāju
        $sqlDzests = $conn->prepare("SELECT vards FROM lasitaji WHERE id=? AND statuss='aktīvs'");
        $sqlDzests->bind_param("i", $id);
        $sqlDzests->execute();
        $lasitajs = $sqlDzests->get_result()->fetch_assoc();
        $sqlDzests->close();

        if (!$lasitajs) {
            http_response_code(404);
            echo json_encode(['error' => false, 'message' => 'Lasītājs nav atrasts!']);
            exit();
        }
        $vards = $lasitajs['vards'];

        // transakcija, lai varētu gan atcelt rezervācijas, gan dzēst lasītāju kopīgi
        $conn->begin_transaction();
        try {
            // atbrīvo visas lasītāja rezervētās grāmatas
            $sqlGramatas = $conn->prepare("
                UPDATE gramatas SET statuss = 'pieejams' 
                WHERE id IN (SELECT gramata_id FROM rezervacijas WHERE lasitajs_id = ? AND statuss IN ('gaida', 'aktīva'))
            ");
            $sqlGramatas->bind_param("i", $id);
            $sqlGramatas->execute();
            $sqlGramatas->close();

            // atceļ rezervācijas
            $sqlRezerv = $conn->prepare("
                UPDATE rezervacijas SET statuss = 'atcelta' 
                WHERE lasitajs_id = ? AND statuss IN ('gaida', 'aktīva')
            ");
            $sqlRezerv->bind_param("i", $id);
            $sqlRezerv->execute();
            $sqlRezerv->close();

            // dzēš lasītāju
            $sql = $conn->prepare("UPDATE lasitaji SET statuss='dzēsts' WHERE id=?");
            $sql->bind_param("i", $id);
            $sql->execute();
            $sql->close();

            // Reģistrē auditā
            $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
            $apraksts = "$vards (#$id)";
            pievienotLogu($conn, $darbinieks, 'Dzēsts', 'Lasītāji', $apraksts);

            // Apstiprinām izmaiņas
            $conn->commit();

            echo json_encode(["success" => true, "message" => "Lasītājs dzēsts un visas rezervācijas atceltas!"]);

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode(["error" => "Neizdevās dzēst lasītāju un/vai atcelt rezervācijas!"]);
        }

        $conn->close();
        exit();
    }
?>