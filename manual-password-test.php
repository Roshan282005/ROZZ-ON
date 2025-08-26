<?php
// Manual password test
echo "<h1>Manual Password Test</h1>";

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

// Get the specific user
$email = "test@example.com";
$stmt = $conn->prepare("SELECT * FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<h2>User Details:</h2>";
    echo "<pre>" . print_r($user, true) . "</pre>";
    
    // Test password verification
    $test_password = "password123";
    $hashed_password = $user['password'];
    
    echo "<h2>Password Verification:</h2>";
    echo "<p><strong>Test Password:</strong> $test_password</p>";
    echo "<p><strong>Hashed Password:</strong> $hashed_password</p>";
    
    $is_valid = password_verify($test_password, $hashed_password);
    echo "<p><strong>Password Valid:</strong> " . ($is_valid ? "YES" : "NO") . "</p>";
    
    // Test what password_hash produces
    $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
    echo "<p><strong>New Hash of 'password123':</strong> $new_hash</p>";
    
    // Test if the new hash verifies
    $new_is_valid = password_verify($test_password, $new_hash);
    echo "<p><strong>New Hash Valid:</strong> " . ($new_is_valid ? "YES" : "NO") . "</p>";
    
    // Test if stored hash verifies with new hash
    $stored_vs_new = password_verify($test_password, $hashed_password);
    echo "<p><strong>Stored Hash vs Test Password:</strong> " . ($stored_vs_new ? "YES" : "NO") . "</p>";
    
} else {
    echo "<p>User test@example.com not found!</p>";
}

$stmt->close();
$conn->close();
?>
