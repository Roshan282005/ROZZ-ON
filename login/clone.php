<?php
require_once 'connect.php'; // Includes .env and DB connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Firebase + PHP Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: #111;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    input, button {
      padding: 10px;
      margin: 8px;
      width: 250px;
      border-radius: 5px;
      border: none;
      font-size: 16px;
    }
    .password-field {
      position: relative;
    }
    .toggle-eye {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
    }
    .google-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      background: #4285F4;
      color: white;
      border-radius: 5px;
      padding: 10px;
      cursor: pointer;
      margin-top: 10px;
    }
    .google-btn i {
      margin-right: 8px;
    }
  </style>
</head>
<body>

  <h2>Register / Login</h2>
  <input type="text" id="firstname" placeholder="First Name">
  <input type="text" id="lastname" placeholder="Last Name">
  <input type="email" id="email" placeholder="Email">
  <div class="password-field">
    <input type="password" id="password" placeholder="Password">
    <i class="fas fa-eye toggle-eye" onclick="togglePassword()"></i>
  </div>
  <button onclick="handleRegister()">Register</button>

  <div class="google-btn" id="googleSignInBtn">
    <i class="fab fa-google"></i> Sign in with Google
  </div>

  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getAuth, createUserWithEmailAndPassword, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

    const firebaseConfig = {
      apiKey: "<?php echo $_ENV['FIREBASE_API_KEY']; ?>",
      authDomain: "your-project-id.firebaseapp.com",
      projectId: "your-project-id",
      storageBucket: "your-project-id.appspot.com",
      messagingSenderId: "your-sender-id",
      appId: "your-app-id"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();

    document.getElementById("googleSignInBtn").addEventListener("click", async () => {
      try {
        const result = await signInWithPopup(auth, provider);
        const user = result.user;

        const res = await fetch("save-user.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            uid: user.uid,
            name: user.displayName,
            email: user.email
          })
        });

        const data = await res.text();
        console.log(data);
        alert("✅ Google login success!");
      } catch (err) {
        console.error("❌ Google Sign-In Failed:", err);
        alert("Google login failed!");
      }
    });

    window.handleRegister = async () => {
      const fname = document.getElementById("firstname").value.trim();
      const lname = document.getElementById("lastname").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;

      if (!fname || !lname || !email || !password) {
        alert("⚠️ Please fill all fields");
        return;
      }

      try {
        const result = await createUserWithEmailAndPassword(auth, email, password);
        const user = result.user;

        const res = await fetch("signup.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            uid: user.uid,
            name: fname + " " + lname,
            email: email,
            password: password
          })
        });

        const data = await res.text();
        console.log(data);
        alert("✅ Registration success!");
      } catch (err) {
        console.error("❌ Registration failed:", err);
        alert("Registration failed!");
      }
    };
  </script>

  <script>
    function togglePassword() {
      const pwField = document.getElementById("password");
      const eye = document.querySelector(".toggle-eye");
      if (pwField.type === "password") {
        pwField.type = "text";
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");
      } else {
        pwField.type = "password";
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
      }
    }
  </script>
</body>
</html>
