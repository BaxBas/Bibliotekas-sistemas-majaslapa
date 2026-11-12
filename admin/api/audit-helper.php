<?php
require_once __DIR__ . '/../../database/db_connect.php';
function pievienotLogu($conn, $darbinieks, $darbiba, $tabula, $ieraksta_nosaukums) {
    // Sagatavojam vaicājumu ar mysqli
    $sql = $conn->prepare("INSERT INTO audit_zurnals (darbinieks, darbiba, tabula, ieraksta_nosaukums) VALUES (?, ?, ?, ?)");
    
    if ($sql) {
        $sql->bind_param("ssss", $darbinieks, $darbiba, $tabula, $ieraksta_nosaukums);
        $sql->execute();
        $sql->close();
    }
}
?>