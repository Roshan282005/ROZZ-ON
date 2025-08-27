<?php
$conn = new mysqli('localhost', 'root', '', 'login');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$sql = 'CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NOT NULL,
    FOREIGN KEY (email) REFERENCES firebase_users(email) ON DELETE CASCADE,
    INDEX idx_email (email),
    INDEX idx_attempt_time (attempt_time)
)';

if ($conn->query($sql) === TRUE) {
    echo 'login_attempts table created successfully!';
} else {
    echo 'Error: ' . $conn->error;
}
$conn->close();
?>
