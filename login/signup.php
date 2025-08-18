<?php
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$uid = $data['uid'];
$email = $data['email'];
$first_name = $data['first_name'];
$last_name = $data['last_name'];

$stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $uid, $email, $first_name, $last_name);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>


