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

    $metode = $_SERVER['REQUEST_METHOD'];

    if ($metode === 'GET') {
        $limits = 20;
        $lapa = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($lapa - 1) * $limits;

        // saskaita, cik ir rezervāciju
        $kopaRez = $conn->query("
            SELECT COUNT(*) AS kopa FROM rezervacijas
        ");
        $kopa = (int)$kopaRez->fetch_assoc()['kopa'];

        $sql = $conn->prepare("SELECT 
            r.id, 
            r.statuss,
            r.rezervacijas_datums,
            r.atdosanas_datums,
            CONCAT(l.vards, ' ', l.uzvards) AS lasitajs, 
            l.epasts,
            g.nosaukums AS gramata
            FROM rezervacijas AS r
            LEFT JOIN lasitaji AS l ON r.lasitajs_id = l.id
            LEFT JOIN gramatas AS g ON r.gramata_id = g.id
            ORDER BY r.id DESC LIMIT ? OFFSET ?");
        $sql->bind_param("ii", $limits, $offset);
        $sql->execute();
        $rezultats = $sql->get_result();

        $rezervacijas = [];
        while ($row = $rezultats->fetch_assoc()) {
            $rezervacijas[] = $row;
        }

        echo json_encode([
            "dati" => $rezervacijas,
            "kopa" => $kopa
        ]);

        $sql->close();
    }

    elseif ($metode === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $statuss = trim($_POST['statuss'] ?? '');

        $atlautie_statusi = ['gaida', 'aktīva', 'pabeigta', 'atcelta'];

        if (!$id || !in_array($statuss, $atlautie_statusi)) {
            http_response_code(400);
            echo json_encode(["error" => "Derīgi dati nav norādīti!"]);
            exit;
        }

        if($statuss == 'pabeigta' || $statuss == 'atcelta'){
            $sqlGramata = $conn->prepare("UPDATE gramatas SET statuss = 'pieejams' 
                WHERE id = (SELECT gramata_id FROM rezervacijas WHERE id = ?)
            ");
            $sqlGramata->bind_param("i", $id);
            $sqlGramata->execute();
            $sqlGramata->close();
        }

        if($statuss == 'pabeigta'){
            $sql = $conn->prepare("UPDATE rezervacijas SET statuss = ?, atdosanas_datums = NOW() WHERE id = ?");
            $sql->bind_param("si", $statuss, $id);
        }else if($statuss == 'atcelta'){
            $sql = $conn->prepare("UPDATE rezervacijas SET statuss = ? WHERE id = ?");
            $sql->bind_param("si", $statuss, $id);
        }else{
            $sql = $conn->prepare("UPDATE rezervacijas SET statuss = ? WHERE id = ?");
            $sql->bind_param("si", $statuss, $id);
        }

        if ($sql->execute()) {
            echo json_encode(["success" => true, "message" => "Statuss veiksmīgi atjaunots!"]);

            // reģistrē darbību
            $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
            $ieraksts = "Rezervācija #$id ($statuss)";
            pievienotLogu($conn, $darbinieks, 'Mainīts statuss', 'Rezervācijas', $ieraksts);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Neizdevās atjaunot statusu!"]);
        }
        $sql->close();
    } 
    else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Metode nav atbalstīta"]);
    }

    $conn->close();
?>