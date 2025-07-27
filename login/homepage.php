<?php
session_start();
include("connect.php");


// Fetch user data
$email = $_SESSION['email'];
$query = $conn->prepare("SELECT * FROM firebase_users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo "User not found!";
    exit();
}
$_SESSION['username'] = $user['name'];
$_SESSION['email'] = $user['email'];
if (!isset($_SESSION['email'])) {
    header("Location: register.php"); // or your login page
    exit();
}
if ($user) {
  echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']);
  echo "<p>" . htmlspecialchars($user['name']) . "</p>";
  echo "<p>" . htmlspecialchars($user['email']) . "</p>";
  echo "<p>" . htmlspecialchars($user['uid']) . "</p>";
 } else {
  echo "User not found!";
}
?>
</p>

        
