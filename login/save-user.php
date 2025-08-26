<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "login";

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
$required_fields = ['first_name', 'last_name', 'email', 'password'];
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

// Validate email format
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Validate password length
if (strlen($input['password']) < 6) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Password must be at least 6 characters long'
    ]);
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $input['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'message' => 'Email already exists'
    ]);
    exit();
}
$stmt->close();

// Generate a unique user ID (simulating Firebase UID)
$uid = uniqid('rizz_', true);

// Hash the password
$hashed_password = password_hash($input['password'], PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sssss", $uid, $input['email'], $input['first_name'], $input['last_name'], $hashed_password);

if ($stmt->execute()) {
    $stmt->close();
    
    // Create user session
    session_start();
    $_SESSION['user_id'] = $uid;
    $_SESSION['user_email'] = $input['email'];
    $_SESSION['user_name'] = $input['first_name'] . ' ' . $input['last_name'];
    
    echo json_encode([
        'status' => 'success',
        'message' => 'User registered successfully',
        'user' => [
            'uid' => $uid,
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name']
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to register user: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
