<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteka";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Sistēmas kļūda savienojoties ar DB: " . mysqli_connect_error());
}
?>