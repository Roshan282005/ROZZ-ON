<?php
// Check all email addresses in database
echo "<h1>Email Address Check</h1>";

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

// Get all email addresses
$emails = $conn->query("SELECT id, email FROM firebase_users");
echo "<h2>All Email Addresses:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Email</th></tr>";

if ($emails->num_rows > 0) {
    while ($row = $emails->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='2'>No users found</td></tr>";
}

echo "</table>";

// Check if test@example.com exists
$test_email = "test@example.com";
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()['count'];

echo "<h2>Check for test@example.com:</h2>";
echo "<p>Found $count users with email: $test_email</p>";

$stmt->close();
$conn->close();
?>
