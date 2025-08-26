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

// Check if email exists
$stmt = $conn->prepare("SELECT uid, password, first_name, last_name FROM firebase_users WHERE email = ?");
$stmt->bind_param("s", $input['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
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
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email or password'
    ]);
    exit();
}

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
