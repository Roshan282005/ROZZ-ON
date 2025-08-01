<?php
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$uid = $data['uid'];
$email = $data['email'];
$name = $data['name'];

$first_name = $name; // If no splitting available

$stmt = $conn->prepare("INSERT IGNORE INTO firebase_users (uid, email, first_name) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $uid, $email, $first_name);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>