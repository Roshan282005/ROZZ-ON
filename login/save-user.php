<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['email'], $data['first_name'], $data['last_name'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit();
}

$conn = new mysqli("localhost", "root", "", "login");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit();
}

// Assign values
$uid = isset($data['uid']) ? $data['uid'] : null;
$email = $data['email'];
$first = $data['first_name'];
$last = $data['last_name'];
$password = isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;

// Prepare insert/update
$stmt = $conn->prepare("
    INSERT INTO firebase_users (uid, email, first_name, last_name, password, login_count, created_at)
    VALUES (?, ?, ?, ?, ?, 1, NOW())
    ON DUPLICATE KEY UPDATE login_count = login_count + 1
");

$stmt->bind_param("sssss", $uid, $email, $first, $last, $password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>