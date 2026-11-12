<?php
    session_start();
    if (!isset($_SESSION['loggedin'])) { 
        header("Location: admin_login.php"); 
        exit(); 
    }

    // Ja kādu iemeslu dēļ šie mainīgi nebūs norādīti - drošības pēc
    $page_title   = $page_title ?? 'Vadības panelis';
    $page_js      = $page_js ?? '';
    $body_class   = $body_class ?? '';
    $topbar_title = $topbar_title ?? $page_title;
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgernīca - <?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="admin-script.js" defer></script>
    <?php if ($page_js): ?>
        <script src="<?= htmlspecialchars($page_js) ?>" defer></script>
    <?php endif; ?>
</head>
<body class="<?= htmlspecialchars($body_class) ?>">

    <button id="uz-augsu-btn" onclick="uzAugsu()" title="Uz augšu">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <!-- Hamburger poga tikai sidebar -->
            <button class="sidebar-close-btn" id="menu-close" aria-label="Aizvērt izvēlni">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h2><a href="admin_dashboard.php">Burgernīca</a></h2>
        </div>
        <!-- Navigācija, pieliek klasi "active", ja atrodas konkrēta lapā -->
        <a href="admin_dashboard.php" class="navPoga <?= $current_page === 'admin_dashboard.php' ? 'active' : '' ?>">Kopsavilkums</a>
        <a href="lasitaji.php" class="navPoga <?= $current_page === 'lasitaji.php' ? 'active' : '' ?>">Lasītāji</a>
        <a href="rezervacijas.php" class="navPoga <?= $current_page === 'rezervacijas.php' ? 'active' : '' ?>">Rezervācijas</a>
        <a href="gramatas.php" class="navPoga <?= $current_page === 'gramatas.php' ? 'active' : '' ?>">Grāmatas</a>
        <!-- Pieejams tikai vadītājam -->
        <?php if($_SESSION['loma'] === 'administrators'): ?>
            <a href="darbinieki.php" class="navPoga <?= $current_page === 'darbinieki.php' ? 'active' : '' ?>">Darbinieki</a>
            <a href="audit-zurnals.php" class=" navPoga <?= $current_page === 'audit-zurnals.php' ? 'active' : '' ?>">Žurnālfaili</a>
        <?php endif; ?>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="hamburgerMenuClass">
                <!-- Hamburger -->
                <button class="hamburger-btn" id="menu-toggle" aria-label="Atvērt izvēlni">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="user-info"><?= htmlspecialchars($topbar_title) ?></div>
            </div>
            <div id="user-logout">
                <span class="spanHelloLogout">Sveiki, <?= htmlspecialchars($_SESSION['lietotajs']) ?>!</span>
                <a href="logout.php" class="logout-btn">Iziet</a>
            </div>
        </div>

        <div class="content-container">
            <div id="notif-box" class="alert"></div>