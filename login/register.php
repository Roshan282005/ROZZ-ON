<?php
require_once 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fName = $_POST['fName'] ?? '';
    $lName = $_POST['lName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($fName) || empty($lName) || empty($email) || empty($password)) {
        echo "❌ All fields are required.";
        exit();
    }

    $uid = uniqid("manual_");
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO firebase_users (uid, first_name, last_name, email, password, login_count, created_at, last_login) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
    $stmt->bind_param("sssss", $uid, $fName, $lName, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "✅ Manual registration successful!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ Invalid request.";
}
?>
