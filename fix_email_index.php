<?php
$conn = new mysqli('localhost', 'root', '', 'login');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Add unique index to email field if it doesn't exist
$result = $conn->query("SHOW INDEX FROM firebase_users WHERE Column_name = 'email' AND Key_name != 'PRIMARY'");
if ($result->num_rows == 0) {
    if ($conn->query("ALTER TABLE firebase_users ADD UNIQUE INDEX idx_email (email)")) {
        echo "Added unique index to email field\n";
    } else {
        echo "Error adding index: " . $conn->error . "\n";
    }
} else {
    echo "Email index already exists\n";
}

// Now create the login_attempts table without foreign key first
$sql = 'CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NOT NULL,
    INDEX idx_email (email),
    INDEX idx_attempt_time (attempt_time)
)';

if ($conn->query($sql) === TRUE) {
    echo "login_attempts table created successfully!\n";
    
    // Now add the foreign key constraint
    $fk_sql = "ALTER TABLE login_attempts ADD CONSTRAINT fk_login_attempts_email 
               FOREIGN KEY (email) REFERENCES firebase_users(email) ON DELETE CASCADE";
    
    if ($conn->query($fk_sql) === TRUE) {
        echo "Foreign key constraint added successfully!\n";
    } else {
        echo "Error adding foreign key: " . $conn->error . "\n";
    }
} else {
    echo 'Error: ' . $conn->error . "\n";
}

$conn->close();
?>
