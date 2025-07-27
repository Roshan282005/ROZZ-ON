<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"));

$conn = new mysqli("localhost", "root", "", "login");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "DB connection failed"]);
    exit();
}
$firebaseKey = $_ENV['FIREBASE_API_KEY'];
$uid = $conn->real_escape_string($data->uid);
$email = $conn->real_escape_string($data->email);
$first_name = $conn->real_escape_string($data->first_name);
$last_name = $conn->real_escape_string($data->last_name);
$password = $conn->real_escape_string($data->password);

// Insert into firebase_users
$stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name, password, login_count, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
$stmt->bind_param("sssss", $uid, $email, $first_name, $last_name, $password);

if ($stmt->execute()) {
    echo json_encode(["message" => "User saved successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Error saving user: " . $stmt->error]);
}

$conn->close();
?>
