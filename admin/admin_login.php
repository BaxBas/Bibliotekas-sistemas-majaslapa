<?php
    require 'login-function.php';
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgernīca - Administrācija</title>
    <link rel="stylesheet" href="admin-style.css">
    <script src="admin-script.js" defer></script>
</head>
<body class="loginBody">
    <div class="login-image">
        <h1>Bibliotēka</h1>
        <p>Pārvaldības sistēma un grāmatu kontrole. Piekļuve atļauta tikai autorizētam personālam.</p>
    </div>
    <div class="login-form-container">
        <div class="login-box">
            <h2>Esiet sveicināti!</h2>
            <p class="subtitle">Lūdzu, ievadiet savus piekļuves datus.</p>
            <?php
                 if (isset($_SESSION["kludaLogin"])){
                    echo "<div class='error-message'>".$_SESSION["kludaLogin"]."</div>";
                    unset($_SESSION["kludaLogin"]);
                 }
            ?>  
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Lietotājvārds</label>
                    <input type="text" id="username" name="username" placeholder="Ievadiet lietotājvārdu" value="<?php if(isset($_COOKIE['admin_atcereties'])) echo $_COOKIE['admin_atcereties']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Parole</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword()">Rādīt</span>
                </div>
                
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember" value="1" <?php if(isset($_COOKIE['admin_atcereties'])) echo "checked"; ?>> Atcerēties mani
                    </label>
                </div>
                
                <button type="submit" name="login" class="btn-login">Pieslēgties sistēmai</button>
            </form>
        </div>
    </div>
</body>
</html>