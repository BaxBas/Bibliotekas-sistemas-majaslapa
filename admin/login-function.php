<?php
    session_start();
    // require '../database/db_connect.php';
    require 'api/audit-helper.php';

    if (isset($_POST['login'])) {
        $lietotajs = $_POST['username'];
        $parole = $_POST['password'];

        // Izmantoti prepared statments, lai nevarētu izmantot sql injiekcijas
        $sql = $conn->prepare("SELECT * FROM darbinieki WHERE lietotajvards = ? LIMIT 1");
        $sql->bind_param("s", $lietotajs);
        $sql->execute();
        
        $rezult = $sql->get_result();

        // Parbauda, vai dotais lietotajs tiešām ir viens un parbauda paroles hash, nevis hardcoded tekstu
        if ($rezult->num_rows === 1) {
            $row = $rezult->fetch_assoc();
            if(password_verify($parole, $row["parole"])){
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['lietotajs'] = $row['lietotajvards'];
                $_SESSION['loma'] = $row['loma'];
                
                if (isset($_POST['remember'])) {
                    setcookie("admin_atcereties", $row['lietotajvards'], time() + (86400 * 30), "/"); // cookie ir derīgs 30 dienas un saglabā tikai lietotājvārdu, "/" nozīmē, ka cookie ir pieejams visā domēnā
                }elseif(isset($_COOKIE['admin_atcereties'])) {
                    unset($_COOKIE['admin_atcereties']);
                    setcookie("admin_atcereties", "", time() - 3600, "/"); // Dzēš cookie, ja checkbox tiek atķeksēts
                }
                
                // Reģistrē ielogošanos
                $darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
                pievienotLogu($conn, $darbinieks, 'ielogošanās', '-', '-');

                header("Location: admin_dashboard.php");
                exit();
            }
        } 
        // Izvada kļūdu, ja nav
        $_SESSION["kludaLogin"] = "Lietotājs ar vārdu <strong>$lietotajs</strong> nav atrasts, vai parole ir nepareiza.";
    }
?>