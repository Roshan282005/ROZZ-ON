<?php
require 'connect.php';
require_once 'connect.php';

$firebaseKey = $_ENV['FIREBASE_API_KEY'];
echo "Your Firebase Key is: " . $firebaseKey;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firebaseKey = $_ENV['FIREBASE_API_KEY'];
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $fName    = $_POST['fName'] ?? 'Manual';
    $lName    = $_POST['lName'] ?? 'User';
    $uid      = uniqid("manual_");
    $name     = $fName . " " . $lName;

    // Check if user exists
    $check = $conn->prepare("SELECT id FROM firebase_users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "⚠️ User already registered.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO firebase_users (uid, name, email, password, login_count, created_at, last_login)
                            VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
    $stmt->bind_param("ssss", $uid, $name, $email, $password);
    if ($stmt->execute()) {
        echo "✅ Manual user registered!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
