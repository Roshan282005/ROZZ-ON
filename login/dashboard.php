<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
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
    die("Database connection failed: " . $conn->connect_error);
}

// Get user information
$stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM firebase_users WHERE uid = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $created_at);
$stmt->fetch();
$stmt->close();

// Get recent failed login attempts
$attempts_stmt = $conn->prepare("
    SELECT COUNT(*) as recent_attempts 
    FROM login_attempts 
    WHERE email = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
");
$attempts_stmt->bind_param("s", $email);
$attempts_stmt->execute();
$attempts_stmt->bind_result($recent_attempts);
$attempts_stmt->fetch();
$attempts_stmt->close();

// Check if account is currently locked
$is_locked = $recent_attempts >= 5;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ℝ𝕀ℤℤ</title>
    <link rel="icon" href="../Rozz.png">
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../rizz.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .welcome-message {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .user-info {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .user-info h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .info-item h3 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .info-item p {
            color: #666;
            margin: 0;
            font-size: 0.9rem;
        }

        .actions {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }

        .btn-logout {
            padding: 12px 24px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
        }

        .nav-back {
            margin-bottom: 1rem;
        }

        .nav-back a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .nav-back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="nav-back">
            <a href="../index.html">← Back to Home</a>
        </div>

        <div class="dashboard-header">
            <h1>ℝ𝕀ℤℤ</h1>
            <p class="welcome-message">Welcome to your dashboard, <?php echo htmlspecialchars($first_name); ?>!</p>
        </div>

        <div class="user-info">
            <h2>Your Profile Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <h3>Full Name</h3>
                    <p><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></p>
                </div>
                <div class="info-item">
                    <h3>Email Address</h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="info-item">
                    <h3>User ID</h3>
                    <p><?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
                </div>
                <div class="info-item">
                    <h3>Member Since</h3>
                    <p><?php echo date('F j, Y', strtotime($created_at)); ?></p>
                </div>
            </div>
        </div>

        <div class="user-info">
            <h2>Security Information</h2>
            <div class="info-grid">
                <div class="info-item" style="border-left-color: <?php echo $is_locked ? '#e74c3c' : '#27ae60'; ?>;">
                    <h3>Account Status</h3>
                    <p>
                        <?php 
                        if ($is_locked) {
                            echo '<span style="color: #e74c3c; font-weight: 600;">🔒 Temporarily Locked</span><br>';
                            echo '<small style="color: #666;">Too many failed login attempts</small>';
                        } else {
                            echo '<span style="color: #27ae60; font-weight: 600;">✅ Active</span><br>';
                            echo '<small style="color: #666;">Your account is secure</small>';
                        }
                        ?>
                    </p>
                </div>
                <div class="info-item">
                    <h3>Recent Failed Attempts</h3>
                    <p>
                        <?php 
                        echo $recent_attempts . ' in last 15 minutes<br>';
                        echo '<small style="color: #666;">' . (5 - $recent_attempts) . ' attempts remaining</small>';
                        ?>
                    </p>
                </div>
                <div class="info-item">
                    <h3>Security Tips</h3>
                    <p style="font-size: 0.8rem; line-height: 1.4;">
                        • Use a strong, unique password<br>
                        • Enable two-factor authentication<br>
                        • Monitor login activity regularly
                    </p>
                </div>
            </div>
        </div>

        <div class="actions">
            <form action="logout.php" method="POST">
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
