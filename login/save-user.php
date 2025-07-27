<?php
require 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$uid = $data['uid'];
$name = $data['name'];
$email = $data['email'];

$stmt = $conn->prepare("INSERT INTO firebase_users (uid, name, email, login_count, created_at, last_login)
                        VALUES (?, ?, ?, 1, NOW(), NOW())
                        ON DUPLICATE KEY UPDATE
                            login_count = login_count + 1,
                            last_login = NOW()");
$stmt->bind_param("sss", $uid, $name, $email);
if ($stmt->execute()) {
    echo "✅ Google user saved!";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
