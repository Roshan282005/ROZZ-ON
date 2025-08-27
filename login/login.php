<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load configuration
require 'config.php';

// Database configuration
$servername = $db_config['host'];
$username = $db_config['username'];
$password = $db_config['password'];
$database = $db_config['database'];

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON input'
    ]);
    exit();
}

// Validate required fields
$required_fields = ['email', 'password'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
        ]);
        exit();
    }
}

// Get client IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

// Check if user is locked out (more than 5 failed attempts in last 15 minutes)
$lockout_check = $conn->prepare("
    SELECT COUNT(*) as attempt_count 
    FROM login_attempts 
    WHERE email = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
");
$lockout_check->bind_param("s", $input['email']);
$lockout_check->execute();
$lockout_check->bind_result($attempt_count);
$lockout_check->fetch();
$lockout_check->close();

if ($attempt_count >= 5) {
    http_response_code(429);
    echo json_encode([
        'status' => 'error',
        'message' => 'Account temporarily locked. Too many failed login attempts. Please try again in 15 minutes.'
    ]);
    exit();
}

// Check if email exists
$stmt = $conn->prepare("SELECT uid, password, first_name, last_name FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $input['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    
    // Log failed attempt for non-existent email (but don't reveal this to user)
    $log_attempt = $conn->prepare("INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)");
    $log_attempt->bind_param("ss", $input['email'], $ip_address);
    $log_attempt->execute();
    $log_attempt->close();
    
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email or password'
    ]);
    exit();
}

$stmt->bind_result($uid, $hashed_password, $first_name, $last_name);
$stmt->fetch();
$stmt->close();

// Verify password
if (!password_verify($input['password'], $hashed_password)) {
    // Log failed attempt
    $log_attempt = $conn->prepare("INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)");
    $log_attempt->bind_param("ss", $input['email'], $ip_address);
    $log_attempt->execute();
    $log_attempt->close();
    
    // Get current attempt count for error message
    $count_check = $conn->prepare("
        SELECT COUNT(*) as current_attempts 
        FROM login_attempts 
        WHERE email = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
    ");
    $count_check->bind_param("s", $input['email']);
    $count_check->execute();
    $count_check->bind_result($current_attempts);
    $count_check->fetch();
    $count_check->close();
    
    $remaining_attempts = 5 - $current_attempts;
    
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email or password. ' . 
                    ($remaining_attempts > 0 ? 
                     "You have $remaining_attempts attempts remaining." : 
                     "Account locked for 15 minutes.")
    ]);
    exit();
}

// Clear failed attempts on successful login
$clear_attempts = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
$clear_attempts->bind_param("s", $input['email']);
$clear_attempts->execute();
$clear_attempts->close();

// Create user session
$_SESSION['user_id'] = $uid;
$_SESSION['user_email'] = $input['email'];
$_SESSION['user_name'] = $first_name . ' ' . $last_name;

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'user' => [
        'uid' => $uid,
        'email' => $input['email'],
        'first_name' => $first_name,
        'last_name' => $last_name
    ]
]);

$conn->close();
?>
