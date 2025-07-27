<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"));

$conn = new mysqli("localhost", "root", "", "login");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "DB connection failed"]);
    exit();
}

$email = $conn->real_escape_string($data->email);
$stmt = $conn->prepare("UPDATE firebase_users SET login_count = login_count + 1 WHERE email = ?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo json_encode(["message" => "Login count updated"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Error updating login: " . $stmt->error]);
}

$conn->close();
?>
