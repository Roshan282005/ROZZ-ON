<?php
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$uid = $data['uid'];
$email = $data['email'];
$first_name = $data['first_name'];
$last_name = $data['last_name'];
$password = $data['password'] ?? null;

if ($password) {
    // Hash the password if provided
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
} else {
    $hashed_password = null;
}

$login_count = 1; // Initialize login_count to 1 on signup

// Prepare statement with password and other fields including login_count, created_at, last_login
$stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name, password, login_count, created_at, last_login) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sssssi", $uid, $email, $first_name, $last_name, $hashed_password, $login_count);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
