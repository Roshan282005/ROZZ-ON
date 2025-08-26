<?php
// Check password verification
echo "<h1>Password Verification Test</h1>";

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "login";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get the user's hashed password
$email = "test@example.com";
$stmt = $conn->prepare("SELECT password FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "<p>User not found!</p>";
    exit();
}

$stmt->bind_result($hashed_password);
$stmt->fetch();
$stmt->close();

echo "<h2>Password Information:</h2>";
echo "<p><strong>Email:</strong> $email</p>";
echo "<p><strong>Hashed Password:</strong> $hashed_password</p>";

// Test password verification
$test_password = "password123";
$is_valid = password_verify($test_password, $hashed_password);

echo "<h2>Password Verification:</h2>";
echo "<p><strong>Test Password:</strong> $test_password</p>";
echo "<p><strong>Password Valid:</strong> " . ($is_valid ? "YES" : "NO") . "</p>";

// Test different passwords
echo "<h2>Other Password Tests:</h2>";
$passwords_to_test = [
    "password123",
    "password",
    "test123",
    "wrongpassword"
];

foreach ($passwords_to_test as $pwd) {
    $valid = password_verify($pwd, $hashed_password);
    echo "<p><strong>$pwd:</strong> " . ($valid ? "VALID" : "INVALID") . "</p>";
}

$conn->close();
?>
