<?php
    session_start();

    if (isset($_POST['veikt_rezervaciju'])) {
        require_once 'database/db_connect.php'; 
        require_once 'vendor/autoload.php';

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $token = $_ENV['token'];

        $lasitaja_epasts = $_POST['epasts'];

        $lasitaja_vards = $_POST['vards'];
        $lasitaja_uzvards = $_POST['uzvards'];

        $parb = $conn->prepare("SELECT id FROM lasitaji WHERE epasts = ? AND statuss = 'aktīvs'");
        $parb->bind_param("s", $lasitaja_epasts);
        $parb->execute();
        $rez = $parb->get_result()->fetch_assoc();
        $lasitaja_id = $rez ? (int)$rez['id'] : 0;
        $parb->close();

        if($lasitaja_id === 0) {
            $liet_izv = $conn->prepare("INSERT INTO lasitaji (vards, uzvards, epasts, statuss) VALUES (?, ?, ?, 'aktīvs')");
            $liet_izv->bind_param("sss", $lasitaja_vards, $lasitaja_uzvards, $lasitaja_epasts);

            //e-pasta verifikācija ar EasyEmailAPI
            $url = "https://easyemailapi.com/api/verify/" . urlencode($lasitaja_epasts);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if ($response === false) {
                $_SESSION['kluda'] = true;
                $_SESSION['kludas_zinojums'] = "E-pasta verifikācija nav pieejama!";
                exit;
            }
            curl_close($ch);
        
            $data = json_decode($response, true);
            $valid = false;

            if (isset($data['score']) && isset($data['max_score']) && $data['score'] >= 0.8 * $data['max_score']) {
                $valid = true;
            }

            if (!$valid) {
                $_SESSION['kluda'] = true;
                $_SESSION['kludas_zinojums'] = "Ievadītais e-pasts nav derīgs vai ir aizdomīgs!";
                header("Location: index.php");
                exit;
            }

            if($liet_izv->execute()){
                $lasitaja_id = $liet_izv->insert_id;
            }else{
                $_SESSION['kluda'] = true;
                $_SESSION['kludas_zinojums'] = "Kļūda jauna lasītāja izveidošanā!";
                header("Location: index.php");
                exit;
            }

            $liet_izv->close();
            
        }

        $groza_dati = json_decode($_POST['groza_dati'], true) ?? [];

        if (!empty($groza_dati) && $lasitaja_id > 0) {

            $gramatu_id_saraksts = array_unique(array_map(function($item) {
                return (int)$item['id'];
            }, $groza_dati));

            $jauno_sk = count($gramatu_id_saraksts);

            $rezervacijas = $conn->prepare("SELECT COUNT(*) AS skaits FROM rezervacijas WHERE lasitajs_id = ? AND statuss != 'pabeigta' AND statuss != 'atcelta'");
            $rezervacijas->bind_param("i", $lasitaja_id);
            if($rezervacijas->execute()){
                $rez = $rezervacijas->get_result()->fetch_assoc();
                $rezervaciju_sk = (int)$rez['skaits'];
                $rezervacijas->close();
            }else{
                $rezervacijas->close();
                $_SESSION['kluda'] = true;
                $_SESSION['kludas_zinojums'] = "Kļūda rezervāciju skaita iegūšanā!";
                header("Location: index.php");
                exit;
            }

            if (($rezervaciju_sk + $jauno_sk) > 3) {
                $_SESSION['kluda'] = true;
                $_SESSION['kludas_zinojums'] = "Vienlaicīgi drīkst būt rezervētas vai paņemtas 3 grāmatas!";
                header("Location: index.php");
                exit();
            }

            // uzsākt vienlaicīgas sql darbības
            $conn->begin_transaction();

            try {
                $rezerv = $conn->prepare("INSERT INTO rezervacijas (lasitajs_id, gramata_id) VALUES (?, ?)");
                $gramatas = $conn->prepare("UPDATE gramatas SET statuss = 'rezervēts' WHERE id = ? AND statuss = 'pieejams'");

                foreach ($gramatu_id_saraksts as $gramatas_id) {
                    // izveido rezervācijas ierakstu
                    $rezerv->bind_param("ii", $lasitaja_id, $gramatas_id);
                    $rezerv->execute();

                    // nomaina grāmatu statusus
                    $gramatas->bind_param("i", $gramatas_id);
                    $gramatas->execute();
                }

                $rezerv->close();
                $gramatas->close();

                $conn->commit();

                $conn->commit();
                $_SESSION['gatavs'] = true;

                // --- PHPMailer E-pasta nosūtīšana ---
                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $_ENV['SMTP_USER'];
                    $mail->Password   = $_ENV['SMTP_PASS'];
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    // Sūtītājs un saņēmējs
                    $mail->setFrom($_ENV['SMTP_USER'], 'Bibliotēka');
                    $mail->addAddress($lasitaja_epasts, $lasitaja_vards . ' ' . $lasitaja_uzvards);

                    // Saturs
                    $mail->isHTML(true);
                    $mail->Subject = 'Rezervācija ir veiksmīgi noformēta';
                    $mail->Body = "Sveiki, <b>{$lasitaja_vards}</b>!<br><br>Jūsu grāmatu rezervācija ir veiksmīgi saņemta un apstrādāta.<br>Droši varat doties uz bibliotēku pakaļ rezervētajām grāmatām!<br><br>Ar cieņu,<br>Bibliotēkas komanda.";
                    $mail->AltBody = "Sveiki! Jūsu grāmatu rezervācija ir veiksmīgi saņemta un apstrādāta.";

                    $mail->send();
                } catch (Exception $e) {
                    // Ja e-pasts nenonoiet, ierakstām logā, lai neapturētu lietotāja plūsmu
                    error_log("E-pasta sūtīšanas kļūda: " . $mail->ErrorInfo);
                }
                // ------------------------------------

            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['kluda'] = true;
            }
        } else {
            $_SESSION['kluda'] = true;
            $_SESSION['kludas_zinojums'] = "Kļūda ar groza datiem!";
        }

        // Redirect after handling POST request
        header("Location: index.php");
        exit();
    }
?>

