<?php
// Test login functionality
echo "<h1>Login Test</h1>";

// Test data (using the same credentials from registration test)
$test_data = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

// Convert to JSON
$json_data = json_encode($test_data);

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, "http://localhost/rizz/login/login.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json_data)
]);

// Execute the request
$response = curl_exec($ch);

// Get HTTP status code
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL
curl_close($ch);

// Display results
echo "<h2>Test Results:</h2>";
echo "<p><strong>HTTP Status Code:</strong> $http_code</p>";
echo "<p><strong>Response:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<h2>Test Data:</h2>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li><a href='http://localhost/rizz/login/dashboard.php'>Test Dashboard</a></li>";
echo "<li><a href='http://localhost/rizz/login/logout.php'>Test Logout</a></li>";
echo "<li><a href='http://localhost/rizz/test-registration.php'>Test Registration Again</a></li>";
echo "</ul>";

// Test dashboard access
echo "<h2>Dashboard Test:</h2>";
$dashboard_response = file_get_contents("http://localhost/rizz/login/dashboard.php");
echo "<pre>" . htmlspecialchars(substr($dashboard_response, 0, 500)) . "...</pre>";
?>
