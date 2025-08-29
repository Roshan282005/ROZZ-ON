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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "login";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    error_log("Invalid JSON input: " . file_get_contents('php://input'));
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON input'
    ]);
    exit();
}

// Required fields for Google users
$required_fields = ['uid', 'first_name', 'last_name', 'email'];
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

// Check if user already exists
$stmt = $conn->prepare("SELECT id FROM firebase_users WHERE uid = ? OR email = ?");
$stmt->bind_param("ss", $input['uid'], $input['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // User already exists → update their info (optional)
    $stmt->close();
    $stmt = $conn->prepare("UPDATE firebase_users SET first_name=?, last_name=? WHERE email=?");
    $stmt->bind_param("sss", $input['first_name'], $input['last_name'], $input['email']);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'User already exists, updated info',
        'user' => $input
    ]);
    exit();
}
$stmt->close();

// Insert new Google user (no password)
$stmt = $conn->prepare("INSERT INTO firebase_users (uid, email, first_name, last_name, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssss", $input['uid'], $input['email'], $input['first_name'], $input['last_name']);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Google user saved successfully',
        'user' => $input
    ]);
} else {
    error_log("Failed to save user: " . $stmt->error);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save user: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
