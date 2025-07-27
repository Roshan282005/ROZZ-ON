<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load .env for local development
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Read environment variables
$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? 'login';

// Create MySQL connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for errors
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
