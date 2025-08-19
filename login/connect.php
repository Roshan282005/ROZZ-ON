<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file once
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 🔍 Test env variables (for debugging only, remove in production)
echo $_ENV['DB_HOST'];          // should print: localhost
echo $_ENV['FIREBASE_API_KEY']; // should print your Firebase key
echo $_ENV['ROZZ_ON'];          // should print: I9JU23NF394R6HH

// Database config
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$db   = $_ENV['DB_NAME'];

// Create DB connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Firebase API key (to use later in your project)
$firebaseKey = $_ENV['FIREBASE_API_KEY'];
?>
