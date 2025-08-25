<?php
// connect.php

// Load env if .env exists (for local dev only)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// MySQL Credentials from env
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = (int)($_ENV['DB_PORT'] ?? 3306);
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? 'login';

// Create connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Firebase Credentials
$FIREBASE_API_KEY             = $_ENV['FIREBASE_API_KEY'] ?? '';
$FIREBASE_AUTH_DOMAIN         = $_ENV['FIREBASE_AUTH_DOMAIN'] ?? '';
$FIREBASE_PROJECT_ID          = $_ENV['FIREBASE_PROJECT_ID'] ?? '';
$FIREBASE_STORAGE_BUCKET      = $_ENV['FIREBASE_STORAGE_BUCKET'] ?? '';
$FIREBASE_MESSAGING_SENDER_ID = $_ENV['FIREBASE_MESSAGING_SENDER_ID'] ?? '';
$FIREBASE_APP_ID              = $_ENV['FIREBASE_APP_ID'] ?? '';
$FIREBASE_MEASUREMENT_ID      = $_ENV['FIREBASE_MEASUREMENT_ID'] ?? '';
$ROZZ_ON                      = $_ENV['ROZZ_ON'] ?? '';
?>
