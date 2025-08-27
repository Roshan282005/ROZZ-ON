<?php
// Test database connection
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default is empty password

try {
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ MySQL connection successful!\n";
    
    // Check if login database exists
    $result = $conn->query("SHOW DATABASES LIKE 'login'");
    if ($result->num_rows > 0) {
        echo "✅ Database 'login' exists\n";
        
        // Check tables in login database
        $conn->select_db("login");
        $tables = $conn->query("SHOW TABLES");
        
        echo "Tables in login database:\n";
        while ($table = $tables->fetch_array()) {
            echo " - " . $table[0] . "\n";
        }
    } else {
        echo "❌ Database 'login' does not exist\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure MySQL is running in XAMPP and the password is correct\n";
}
?>
