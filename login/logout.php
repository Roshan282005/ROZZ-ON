<?php
<<<<<<< HEAD
header("Content-Type: application/json");
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['uid'], $data['email'])) {
    echo json_encode(["status" => "error", "message" => "❌ Missing UID or Email"]);
    exit();
}

$uid = $data['uid'];
$email = $data['email'];

// 🔍 Update the most recent login entry
$sql = "UPDATE login_history 
        SET logout_time = NOW(), is_online = 0 
        WHERE uid = ? AND email = ? 
        ORDER BY login_time DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $uid, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "✅ Logout recorded"]);
} else {
    echo json_encode(["status" => "error", "message" => "⚠️ Logout not recorded"]);
}

$conn->close();
?>
=======
session_destroy();
header("location: index.php");
?>
>>>>>>> 0089aaa8b13156239de150a93f805cc10253258d
