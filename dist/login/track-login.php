<?php
// Allow CORS for frontend hosted separately (like Vercel)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'connect.php';

// Read raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Log the raw input for debugging (optional)
file_put_contents("debug.txt", json_encode($data) . PHP_EOL, FILE_APPEND);

if ($data) {
    // Extract and sanitize data
    $uid = mysqli_real_escape_string($conn, $data['uid'] ?? '');
    $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
    $name = $data['name'] ?? 'Manual User';

    // Split name into first and last
    $nameParts = explode(" ", $name);
    $first_name = mysqli_real_escape_string($conn, $nameParts[0] ?? 'Manual');
    $last_name = mysqli_real_escape_string($conn, $nameParts[1] ?? 'User');

    // Default email_verified to 0 (you can change this later)
    $email_verified = 0;

    // Build SQL query to insert or update user
    $sql = "INSERT INTO firebase_users (uid, email, first_name, last_name, login_count, last_login, email_verified)
            VALUES ('$uid', '$email', '$first_name', '$last_name', 1, NOW(), $email_verified)
            ON DUPLICATE KEY UPDATE 
                login_count = login_count + 1,
                last_login = NOW()";

    // Execute query
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "Login tracked successfully"]);
    } else {
        file_put_contents("error_log.txt", mysqli_error($conn) . PHP_EOL, FILE_APPEND);
        http_response_code(500);
        echo json_encode(["error" => " DB error", "details" => mysqli_error($conn)]);
    }

} else {
    file_put_contents("error_log.txt", "Invalid JSON: " . file_get_contents("php://input") . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(["error" => " Invalid input"]);
}
?>
