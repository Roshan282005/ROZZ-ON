<?php
// Database configuration - Use environment variables in production
$db_config = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'database' => getenv('DB_NAME') ?: 'login',
    'port' => getenv('DB_PORT') ?: 3306
];

// Security settings
$security_config = [
    'lockout_attempts' => 5,
    'lockout_minutes' => 15,
    'require_https' => false // Set to true in production
];

// Application settings
$app_config = [
    'base_url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => 'UTC'
];

// Set timezone
date_default_timezone_set($app_config['timezone']);

// Force HTTPS in production if enabled
if ($security_config['require_https'] && empty($_SERVER['HTTPS'])) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
?>
