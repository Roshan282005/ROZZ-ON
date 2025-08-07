<?php
header("Content-Type: application/json");

// 1. Read JSON body
$data = json_decode(file_get_contents("php://input"), true);

// 2. Validate input
if (!$data || !isset($data['email'], $data['first_name'], $data['last_name'])) {
    echo json_encode(["status" => "error", "message" => "❌ Invalid input"]);
    exit();
}

// 3. DB Connection
$conn = new mysqli("localhost", "root", "", "login");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "❌ DB connection failed"]);
    exit();
}

// 4. Prepare data
$uid = $data['uid'] ?? uniqid('anon_'); // fallback if uid is missing
$email = $data['email'];
$first_name = $data['first_name'];
$last_name = $data['last_name'];
$password = isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;

// 5. Check if user exists
$query = $conn->prepare("SELECT id FROM firebase_users WHERE uid = ?");
$query->bind_param("s", $uid);
$query->execute();
$query->store_result();

if ($query->num_rows > 0) {
    // 6a. Update existing user login count
    $stmt = $conn->prepare("UPDATE firebase_users SET login_count = login_count + 1, last_login = NOW() WHERE uid = ?");
    $stmt->bind_param("s", $uid);
} else {
    // 6b. Insert new user
    if ($password) {
        $stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name, password, login_count, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param("sssss", $uid, $email, $first_name, $last_name, $password);
    } else {
        $stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name, login_count, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param("ssss", $uid, $email, $first_name, $last_name);
    }
}

// 7. Execute and return response
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "✅ User saved"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$conn->close();
?>
