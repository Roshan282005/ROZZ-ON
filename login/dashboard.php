<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
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
    die("Database connection failed: " . $conn->connect_error);
}

// Get user information
$stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM firebase_users WHERE uid = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $created_at);
$stmt->fetch();
$stmt->close();
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
