<?php
// Load configuration
require 'config.php';

// Create connection without selecting database first
$conn = new mysqli($db_config['host'], $db_config['username'], $db_config['password']);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS login CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "Database 'login' created successfully or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db("login");

// Read SQL schema file
$sql_file = __DIR__ . "/database-schema.sql";
if (!file_exists($sql_file)) {
    die("SQL schema file not found: $sql_file");
}

$sql_content = file_get_contents($sql_file);

// Split SQL content into individual statements and filter out comments
$sql_statements = array_filter(array_map('trim', explode(';', $sql_content)));

foreach ($sql_statements as $sql) {
    // Skip comments and empty statements
    if (empty($sql) || strpos($sql, '--') === 0) {
        continue;
    }
    
    if ($conn->query($sql) === TRUE) {
        echo "Executed SQL statement successfully: " . substr($sql, 0, 50) . "...<br>";
    } else {
        echo "Error executing SQL: " . $conn->error . "<br>";
        echo "Statement: " . substr($sql, 0, 100) . "...<br>";
    }
}

echo "Database setup completed!<br>";
echo "Tables should be created: firebase_users, user_sessions, password_reset_tokens<br>";

$conn->close();

echo "<h2>Database Setup Complete</h2>";
echo "<p>The database has been set up successfully. You can now:</p>";
echo "<ul>";
echo "<li><a href='../register.html'>Register a new user</a></li>";
echo "<li><a href='login.html'>Login with existing user</a></li>";
echo "<li><a href='../index.html'>Go to homepage</a></li>";
echo "</ul>";
?>
