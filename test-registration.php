<?php
// Test registration functionality
echo "<h1>Registration Test</h1>";

// Test data
$test_data = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'password' => 'password123'
];

// URL encode the data
$post_data = http_build_query($test_data);

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, "http://localhost/rizz/login/save-user.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

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
echo "<li><a href='http://localhost/rizz/login/login.html'>Test Login</a></li>";
echo "<li><a href='http://localhost/rizz/login/dashboard.php'>Test Dashboard</a></li>";
echo "<li><a href='http://localhost/rizz/login/setup-database.php'>Re-run Database Setup</a></li>";
echo "</ul>";
?>
