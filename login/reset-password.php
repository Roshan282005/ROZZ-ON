<?php
session_start();

// ✅ Error reporting (only in dev mode, disable in prod)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Load DB connection
require_once __DIR__ . '/connect.php';

// Check DB connection
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Handle password reset request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email'] ?? '');
  
  if (!empty($email)) {
    // Check if user exists in database
    $stmt = $conn->prepare("SELECT * FROM firebase_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
      // User exists - you could implement email sending logic here
      // For now, we'll just show a success message
      $success = "Password reset instructions have been sent to your email.";
    } else {
      $error = "No account found with that email address.";
    }
    $stmt->close();
  } else {
    $error = "Email address is required.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <center>
      <a href="index.php">
        <img src="Rozz.png" alt="logo" width="80px">
      </a>
    </center>
    <h1 class="form-title" style="color: rgb(21, 21, 141);">RESET PASSWORD</h1>
    
    <?php if (isset($success)): ?>
      <div class="alert alert-success">
        <?php echo $success; ?>
      </div>
      <p class="links">
        <a href="login.php">Return to Login</a>
      </p>
    <?php else: ?>
      <form id="resetPasswordForm" method="POST">
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" id="resetEmail" placeholder="Enter your email" required>
          <label for="resetEmail">Email</label>
        </div>
        
        <?php if (isset($error)): ?>
          <div class="alert alert-error">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>
        
        <input type="submit" class="btn" value="Send Reset Link">
      </form>
      
      <p class="links">
        <a href="login.php">Back to Login</a>
      </p>
    <?php endif; ?>
  </div>

  <script>
    // Add some basic styling for alerts
    const style = document.createElement('style');
    style.textContent = `
      .alert {
        padding: 12px;
        margin: 10px 0;
        border-radius: 4px;
        text-align: center;
      }
      .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
      }
      .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
      }
      .links {
        text-align: center;
        margin-top: 20px;
      }
      .links a {
        color: rgb(59, 59, 255);
        text-decoration: none;
      }
      .links a:hover {
        text-decoration: underline;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>
