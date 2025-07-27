<?php
require_once 'connect.php';
$firebaseKey = $_ENV['FIREBASE_API_KEY'];
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
<p id="status"></p>

<div class="container" id="signInForm">
  <center>
    <a href="index.html">
      <img src="Rozz.png" alt="logo" width="80px">
    </a>
  </center>
  <h1 class="form-title" style="color: rgb(21, 21, 141);">SIGN IN</h1>
  <form id="loginForm">
    <div class="input-group">
      <i class="fas fa-envelope"></i>
      <input type="email" name="email" id="loginEmail" placeholder="Email" required>
      <label for="loginEmail">Email</label>
    </div>
    <div class="input-group" style="position: relative;">
      <i class="fas fa-lock"></i>
      <input type="password" name="password" id="loginPassword" placeholder="Password" required>
      <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 20px; top: 30%; transform: translateY(-50%); cursor: pointer; opacity: 0.5;"></i>
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
      <i class="fas fa-eye" id="toggleRegisterPassword" style="position: absolute; right: 20px; top: 30%; transform: translateY(-50%); cursor: pointer; opacity: 0.5;"></i>
      <label for="registerPassword">Password</label>
    </div>

    <input type="submit" class="btn" value="Sign Up" name="signUp">
  </form>
  <p class="or">Or</p>
  <div class="icons">
    <label for="googleSignUpBtn">
      <i id="googleSignUpBtn" class="fab fa-google"></i>Google
    </label>
  </div>
  <div class="links">
    <p style="color: rgb(59, 59, 255);">Already Have Account?</p>
    <button id="showSignIn" type="button" class="btn">Sign In</button>
  </div>
</div>

<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js"></script>
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
  import {
    getAuth,
    createUserWithEmailAndPassword,
    signInWithEmailAndPassword,
    GoogleAuthProvider,
    signInWithPopup,
    onAuthStateChanged
  } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

  const firebaseConfig = {
    apiKey: "AIzaSyDoIAiSnBx8GGNhjQkEB7j1bANx7k2l8dc",
    authDomain: "rizzauthapp.firebaseapp.com",
    projectId: "rizzauthapp",
    storageBucket: "rizzauthapp.firebasestorage.app",
    messagingSenderId: "607508317395",
    appId: "1:607508317395:web:f2f403d10915d6d2ef4026",
    measurementId: "G-2YQFBWK95F"
  };

  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);
  const provider = new GoogleAuthProvider();

  // 🔐 Sign Up (Email + Password)
  document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const email = document.getElementById("registerEmail").value;
    const password = document.getElementById("registerPassword").value;
    const fName = document.getElementById("fName").value;
    const lName = document.getElementById("lName").value;
    const fullName = fName + " " + lName;

    createUserWithEmailAndPassword(auth, email, password)
      .then((userCredential) => {
        const user = userCredential.user;

        // Save to MySQL
        fetch("signup.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            uid: user.uid,
            name: fullName,
            email: user.email,
            password: password
          })
        })
        .then(res => res.json())
        .then(msg => {
          console.log("✅ MySQL response:", msg);
          alert("✅ Signed up & saved!");
          window.location.href = "http://localhost/roshans/index.html";
        });
      })
      .catch((error) => {
        alert("❌ Signup error: " + error.message);
      });
  });

  // 🔐 Sign In (Email + Password)
  document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("loginPassword").value;

    signInWithEmailAndPassword(auth, email, password)
      .then((userCredential) => {
        const user = userCredential.user;

        // Track login (update count & last_login)
        fetch("track-login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            uid: user.uid,
            email: user.email,
            name: user.displayName || "Firebase User",
            password: password
          })
        })
        .then(res => res.json())
        .then(data => {
          console.log("📊 Login tracked:", data);
          alert("🎉 Welcome back " + (user.displayName || user.email));
          window.location.href = "http://localhost/roshans/index.html";
        });
      })
      .catch((error) => {
        alert("❌ Login failed: " + error.message);
      });
  });

  // 🔐 Google Sign In
  document.getElementById("googleSignUpBtn").addEventListener("click", () => {
    signInWithPopup(auth, provider)
      .then((result) => {
        const user = result.user;

        fetch("save-user.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            uid: user.uid,
            name: user.displayName,
            email: user.email
          })
        })
        .then(res => res.text())
        .then(data => {
          console.log("✅ Google user saved:", data);
          alert("🎉 Welcome " + user.displayName);
          window.location.href = "http://localhost/roshans/index.html";
        });
      })
      .catch((error) => {
        alert("❌ Google Sign-In failed: " + error.message);
      });
  });

  onAuthStateChanged(auth, (user) => {
    if (user) {
      console.log("✅ Signed in as:", user.email);
    }
  });
</script>

<script>
  document.getElementById('showSignUp').onclick = function () {
    document.getElementById('signInForm').style.display = 'none';
    document.getElementById('signUpForm').style.display = 'block';
  };
  document.getElementById('showSignIn').onclick = function () {
    document.getElementById('signUpForm').style.display = 'none';
    document.getElementById('signInForm').style.display = 'block';
  };
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const loginPassword = document.getElementById("loginPassword");
  const togglePassword = document.getElementById("togglePassword");

  const registerPassword = document.getElementById("registerPassword");
  const toggleRegisterPassword = document.getElementById("toggleRegisterPassword");

  // Login toggle
  togglePassword?.addEventListener("click", function () {
    const isHidden = loginPassword.type === "password";
    loginPassword.type = isHidden ? "text" : "password";
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });

  // Register toggle
  toggleRegisterPassword?.addEventListener("click", function () {
    const isHidden = registerPassword.type === "password";
    registerPassword.type = isHidden ? "text" : "password";
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });
});
</script>
<script>
  const FIREBASE_API_KEY = "<?php echo $_ENV['FIREBASE_API_KEY']; ?>";
</script>
<script>
  const FIREBASE_API_KEY = "<?php echo $firebaseKey; ?>";
  console.log("Firebase Key:", FIREBASE_API_KEY);
</script>

</body>
</html>
