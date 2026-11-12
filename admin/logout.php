<?php
session_start();
require 'api/audit-helper.php';

if (isset($_COOKIE['admin_atcereties'])) {
    unset($_COOKIE['admin_atcereties']);
    setcookie('admin_atcereties', null, -1);
}

// Reģistrē izlogošanos
$darbinieks = $_SESSION['lietotajs'] ?? 'Nezināms';
pievienotLogu($conn, $darbinieks, 'izlogošanās', '-', '-');

// izlogoties no konta
session_destroy();

header("Location: admin_login.php");
?>