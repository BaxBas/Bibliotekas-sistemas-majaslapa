<?php
    header("Content-Type: application/json; charset=UTF-8");
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

    $metode = $_SERVER['REQUEST_METHOD'];

    // Iegūst/mekle visus logus
    if ($metode === 'GET') {
        $limits = 20;
        $lapa = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($lapa - 1) * $limits;
        
        $search_input = trim($_GET['search'] ?? '');
        if (!empty($search_input)) {
            $search = "%" . $search_input . "%";

            // saskaita, cik kopā atbilst meklēšanai
            $kopaSql = $conn->prepare("
                SELECT COUNT(*) AS kopa
                FROM audit_zurnals
                WHERE darbinieks LIKE ? OR darbiba LIKE ? OR tabula LIKE ? OR ieraksta_nosaukums LIKE ?
            ");
            $kopaSql->bind_param("ssss", $search, $search, $search, $search);
            $kopaSql->execute();
            $kopa = (int)$kopaSql->get_result()->fetch_assoc()['kopa'];
            $kopaSql->close();

            $sql = $conn->prepare("
                SELECT id, darbinieks, darbiba, tabula, ieraksta_nosaukums, DATE_FORMAT(laiks, '%d.%m.%Y %H:%i:%s') AS laiks
                FROM audit_zurnals
                WHERE darbinieks LIKE ? OR darbiba LIKE ? OR tabula LIKE ? OR ieraksta_nosaukums LIKE ?
                ORDER BY id DESC
                LIMIT ? OFFSET ?
            ");
            $sql->bind_param("ssssii", $search, $search, $search, $search, $limits, $offset);
            $sql->execute();
            $rezultats = $sql->get_result();

            $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
            pievienotLogu($conn, $darbinieks, 'Meklēts', 'Žurnālfaili', $search_input);
        } else {
            // saskaita, cik ir klientu kopā
            $kopaRez = $conn->query("SELECT COUNT(*) AS kopa FROM audit_zurnals");
            $kopa = (int)$kopaRez->fetch_assoc()['kopa'];

            $sql = $conn->prepare("SELECT id, darbinieks, darbiba, tabula, ieraksta_nosaukums, DATE_FORMAT(laiks, '%d.%m.%Y %H:%i:%s') AS laiks FROM audit_zurnals ORDER BY id DESC LIMIT ? OFFSET ?");
            $sql->bind_param("ii", $limits, $offset);
            $sql->execute();
            $rezultats = $sql->get_result();
        }

        $logi = [];
        while ($log = $rezultats->fetch_assoc()) {
            $logi[] = $log;
        }

        echo json_encode([
            "dati" => $logi,
            "kopa" => $kopa
        ]);

        if (isset($sql) && $sql) {
            $sql->close();
        }
    }
    else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Metode nav atbalstīta"]);
    }

$conn->close();