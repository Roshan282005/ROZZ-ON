<?php
// test_db_connection.php

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'your_database_name'; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!";
}

$conn->close();
?>
