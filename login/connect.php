<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "login";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}
?>
