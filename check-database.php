<?php
// Check database contents
echo "<h1>Database Check</h1>";

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

// Check if firebase_users table exists
$result = $conn->query("SHOW TABLES LIKE 'firebase_users'");
if ($result->num_rows === 0) {
    echo "<p>firebase_users table does not exist!</p>";
    exit();
}

// Get all users
$users = $conn->query("SELECT * FROM firebase_users");
echo "<h2>Users in firebase_users table:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>UID</th><th>Email</th><th>First Name</th><th>Last Name</th><th>Created At</th></tr>";

if ($users->num_rows > 0) {
    while ($row = $users->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['uid']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No users found</td></tr>";
}

echo "</table>";

// Check other tables
$tables = $conn->query("SHOW TABLES");
echo "<h2>All tables in database:</h2>";
echo "<ul>";
while ($table = $tables->fetch_array()) {
    echo "<li>" . htmlspecialchars($table[0]) . "</li>";
}
echo "</ul>";

$conn->close();
?>
