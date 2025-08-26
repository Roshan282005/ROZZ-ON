<?php
// Detailed user check
echo "<h1>Detailed User Check</h1>";

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

// Get all users with all fields
$users = $conn->query("SELECT * FROM firebase_users");
echo "<h2>All Users in firebase_users table:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>UID</th><th>Email</th><th>First Name</th><th>Last Name</th><th>Password</th><th>Created At</th><th>Updated At</th></tr>";

if ($users->num_rows > 0) {
    while ($row = $users->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['uid']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['password'], 0, 20)) . "...</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "<td>" . htmlspecialchars($row['updated_at']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No users found</td></tr>";
}

echo "</table>";

// Check specific user
$email = "test@example.com";
$stmt = $conn->prepare("SELECT * FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Details for test@example.com:</h2>";
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<pre>" . print_r($user, true) . "</pre>";
    
    // Test password verification
    $test_password = "password123";
    $is_valid = password_verify($test_password, $user['password']);
    echo "<p><strong>Password verification for 'password123':</strong> " . ($is_valid ? "VALID" : "INVALID") . "</p>";
} else {
    echo "<p>User test@example.com not found!</p>";
}

$stmt->close();
$conn->close();
?>
