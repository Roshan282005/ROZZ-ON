<?php
header("Content-Type: application/json");
require 'connect.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!$data || !isset($data['uid'], $data['name'], $data['email'])) {
    echo json_encode(["status" => "error", "message" => "❌ Invalid data!"]);
    exit();
}

$uid   = $data['uid'];
$name  = $data['name'];
$email = $data['email'];

// Insert or update user
$stmt = $conn->prepare("INSERT INTO firebase_users (uid, name, email, logins, last_login)
                        VALUES (?, ?, ?, 1, NOW())
                        ON DUPLICATE KEY UPDATE logins = logins + 1, last_login = NOW()");
$stmt->bind_param("sss", $uid, $name, $email);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "✅ User saved"]);
} else {
    echo json_encode(["status" => "error", "message" => "❌ DB Error: " . $stmt->error]);
}
?>
