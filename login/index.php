<?php
session_start();

// ✅ Error reporting (only in dev mode, disable in prod)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Load DB connection
require_once __DIR__ . '/connect.php';

// ✅ Load Firebase Admin SDK if needed
// Make sure you installed firebase/php-jwt and kreait/firebase-php via composer
require_once __DIR__ . '/vendor/autoload.php';

$firebaseKey = getenv("FIREBASE_API_KEY");

// Check DB connection
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Handle login via form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if (!empty($email) && !empty($password)) {
    // ✅ Prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM firebase_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
      // Verify password (hashed)
      if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['login_time'] = date("Y-m-d H:i:s");

        // ✅ Update login count
        $update = $conn->prepare("UPDATE firebase_users SET login_count = login_count + 1, last_login = NOW() WHERE id = ?");
        $update->bind_param("i", $row['id']);
        $update->execute();

        header("Location: dashboard.php");
        exit();
      } else {
        $error = "Invalid password!";
      }
    } else {
      $error = "User not found!";
    }
    $stmt->close();
  } else {
    $error = "Email and password are required!";
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register & Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
</head>

<body>
  <script src="src/index.php"></script>
  <p id="status"></p>

  <div class="container" id="signInForm">
    <center>
      <a href="Public/index.html">
        <img src="Rozz.png" alt="logo" width="80px">
      </a>
    </center>
    <h1 class="form-title" style="color: rgb(21, 21, 141); text-decoration:0 2px 35px 10px rgba(58, 133, 246, 0.9);">SIGN IN</h1>
    <form id="loginForm">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="loginEmail" placeholder="Email" required>
        <label for="loginEmail">Email</label>
      </div>
      <div class="input-group" style="position: relative;">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="loginPassword" placeholder="Password" required>
        <i class="fas fa-eye" id="togglePassword" style="position: absolute; left: 95%; top: 40%; transform: translateY(-50%); cursor: pointer; opacity: 0.5;"></i>
        <label for="loginPassword">Password</label>
      </div>
      <p class="recover">
        <a href="register.php">Forgot Password</a>
      </p>
      <input type="submit" class="btn" value="Sign In" name="signIn">
    </form>
    <p class="or">Or</p>
    <div class="links">
      <p style="color: rgb(59, 59, 255);">Don't have an account yet?</p>
      <button id="showSignUp" class="btn" type="button">Sign Up</button>
    </div>
  </div>

  <div class="container" id="signUpForm" style="display:none;">
    <center>
      <a href="index.html">
        <img src="Rozz.png" alt="logo" width="80px">
      </a>
    </center>
    <h1 class="form-title" style="color: rgb(20, 20, 108); font-weight: bolder;">REGISTER YOUR ACCOUNT</h1>
    <form id="registerForm">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="fName" id="fName" placeholder="First Name" required>
        <label for="fName">First Name</label>
      </div>
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="lName" id="lName" placeholder="Last Name" required>
        <label for="lName">Last Name</label>
      </div>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="registerEmail" placeholder="Email" required>
        <label for="registerEmail">Email</label>
      </div>
      <div class="input-group" style="position: relative;">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="registerPassword" placeholder="Password" required>
        <i class="fas fa-eye" id="toggleRegisterPassword" style="position: absolute; left: 95%; top: 40%; transform: translateY(-50%); cursor: pointer; opacity: 0.5;"></i>
        <label for="registerPassword">Password</label>
      </div>

      <input type="submit" class="btn" value="Sign Up" name="signUp">
    </form>
    <p class="or">Or</p>
    <div class="icons">
      <i id="googleSignUpBtn" class="fab fa-google"></i><br>
      <span style="font-family: 'Product Sans', sans-serif;">
        <span style="color: #4285F4;">G</span>
        <span style="color: #EA4335;">o</span>
        <span style="color: #FBBC05;">o</span>
        <span style="color: #4285F4;">g</span>
        <span style="color: #34A853;">l</span>
        <span style="color: #EA4335;">e</span>
      </span>
    </div>
    <div class="links">
      <p style="color: rgb(59, 59, 255);">Already Have Account?</p>
      <button id="showSignIn" type="button" class="btn">Sign In</button>
    </div>
  </div>

  <script type="module">
    import {
      initializeApp
    } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import {
      getAuth,
      createUserWithEmailAndPassword,
      signInWithEmailAndPassword,
      GoogleAuthProvider,
      signInWithPopup
    } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

    const firebaseConfig = {
      apiKey: "AIzaSyDoIAiSnBx8GGNhjQkEB7j1bANx7k2l8dc",
      authDomain: "rizzauthapp.firebaseapp.com",
      projectId: "rizzauthapp",
      storageBucket: "rizzauthapp.appspot.com",
      messagingSenderId: "607508317395",
      appId: "1:607508317395:web:f2f403d10915d6d2ef4026",
      measurementId: "G-2YQFBWK95F"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();

    // Manual Signup
    document.getElementById("registerForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const fName = document.getElementById("fName").value;
      const lName = document.getElementById("lName").value;
      const email = document.getElementById("registerEmail").value;
      const password = document.getElementById("registerPassword").value;

      try {
        const result = await createUserWithEmailAndPassword(auth, email, password);
        const user = result.user;

        await fetch("https://php-backend.onrender.com/login/signup.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            uid: user.uid,
            email,
            first_name: fName,
            last_name: lName
          })
        });


        alert("Signup successful");
        window.location.href = "https://rizz-yourvercel.vercel.app/index.html";
      } catch (err) {
        alert("Signup error: " + err.message);
      }
    });

    // Manual Login
    document.getElementById("loginForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = document.getElementById("loginEmail").value;
      const password = document.getElementById("loginPassword").value;

      try {
        const result = await signInWithEmailAndPassword(auth, email, password);
        const user = result.user;

        // Send to backend as FormData
        const formData = new FormData();
        formData.append("uid", user.uid);
        formData.append("email", user.email);
        formData.append("name", user.displayName || "Manual User");

        await fetch("https://php-backend.onrender.com/login/track-login.php", {
          method: "POST",
          body: formData
        });

        alert("Welcome back!");
        window.location.href = "https://rizz-yourvercel.vercel.app/index.html";
      } catch (err) {
        alert("Login failed: " + err.message);
      }
    });

    // Google Login
    document.getElementById("googleSignUpBtn").addEventListener("click", async () => {
      try {
        const result = await signInWithPopup(auth, provider);
        const user = result.user;

        const [first_name, ...rest] = user.displayName?.split(" ") ?? ["Google"];
        const last_name = rest.join(" ") || "";

        const response = await fetch("https://php-backend.onrender.com/login/save-user.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            uid: user.uid,
            email: user.email,
            first_name,
            last_name
          })
        });

        try {
          const data = await response.json();
          console.log("Response from PHP:", data);
          alert("Saved to DB: " + user.displayName + " (" + user.email + ")");
        } catch (err) {
          console.error("Fetch error:", err);
          alert("Fetch failed");
        }

        alert("Google login success!");
        window.location.href = "https://rizz-yourvercel.vercel.app/index.html";
      } catch (err) {
        console.error("Google Sign-In Failed:", err);
        alert("Google login failed: " + err.message);
      }
    });
  </script>

  <script>
    // Toggle forms
    document.getElementById('showSignUp').onclick = () => {
      document.getElementById('signInForm').style.display = 'none';
      document.getElementById('signUpForm').style.display = 'block';
    };
    document.getElementById('showSignIn').onclick = () => {
      document.getElementById('signUpForm').style.display = 'none';
      document.getElementById('signInForm').style.display = 'block';
    };

    // Toggle password visibility
    document.addEventListener("DOMContentLoaded", () => {
      const loginPassword = document.getElementById("loginPassword");
      const togglePassword = document.getElementById("togglePassword");
      const registerPassword = document.getElementById("registerPassword");
      const toggleRegisterPassword = document.getElementById("toggleRegisterPassword");

      togglePassword?.addEventListener("click", () => {
        loginPassword.type = loginPassword.type === "password" ? "text" : "password";
        togglePassword.classList.toggle("fa-eye");
        togglePassword.classList.toggle("fa-eye-slash");
      });

      toggleRegisterPassword?.addEventListener("click", () => {
        registerPassword.type = registerPassword.type === "password" ? "text" : "password";
        toggleRegisterPassword.classList.toggle("fa-eye");
        toggleRegisterPassword.classList.toggle("fa-eye-slash");
      });
    });
  </script>

</body>

</html>