<?php
$conn = new mysqli('localhost', 'root', '', 'login');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$result = $conn->query('DESCRIBE firebase_users');
echo "firebase_users table structure:\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

$result = $conn->query('SHOW INDEX FROM firebase_users WHERE Key_name = "PRIMARY" OR Key_name = "idx_email"');
echo "\nIndexes on firebase_users:\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Key_name'] . ' - ' . $row['Column_name'] . "\n";
}

$conn->close();
?>
