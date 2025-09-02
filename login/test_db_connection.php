<?php
// test_db_connection.php

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'login'; // Database name from schema

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!";
}

$conn->close();
?>
