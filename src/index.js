// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Password toggle functionality
document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("loginPassword");

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  }

  // Add login button event listener if it exists
  const signInButton = document.getElementById("signInButton");
  if (signInButton) {
    signInButton.addEventListener("click", loginFunction);
  }
});

// Enhanced login function with error handling and user feedback
function loginFunction() {
  const email = document.getElementById("loginEmail")?.value;
  const password = document.getElementById("loginPassword")?.value;
  
  if (!email || !password) {
    showMessage("Please fill in all fields", "error");
    return;
  }

  // Show loading state
  const signInButton = document.getElementById("signInButton");
  const originalText = signInButton.textContent;
  signInButton.textContent = "Signing in...";
  signInButton.disabled = true;

  firebase.auth().signInWithEmailAndPassword(email, password)
    .then((userCredential) => {
      // Signed in successfully
      showMessage("Login successful! Redirecting...", "success");
      
      // Send login data to backend
      const user = userCredential.user;
      fetch("login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          uid: user.uid,
          email: user.email,
          displayName: user.displayName
        })
      })
      .then(res => res.json())
      .then(data => {
        console.log("Backend response:", data);
        // Redirect after successful login
        setTimeout(() => {
          window.location.href = "homepage.php";
        }, 1500);
      })
      .catch(error => {
        console.error("Backend error:", error);
        showMessage("Login successful, but backend error occurred", "warning");
      });
    })
    .catch((error) => {
      // Handle login errors
      let errorMessage = "Login failed. Please try again.";
      
      switch (error.code) {
        case "auth/invalid-email":
          errorMessage = "Invalid email address format.";
          break;
        case "auth/user-disabled":
          errorMessage = "This account has been disabled.";
          break;
        case "auth/user-not-found":
          errorMessage = "No account found with this email.";
          break;
        case "auth/wrong-password":
          errorMessage = "Incorrect password.";
          break;
        case "auth/too-many-requests":
          errorMessage = "Too many failed attempts. Please try again later.";
          break;
      }
      
      showMessage(errorMessage, "error");
    })
    .finally(() => {
      // Restore button state
      if (signInButton) {
        signInButton.textContent = originalText;
        signInButton.disabled = false;
      }
    });
}

// Show message to user
function showMessage(message, type = "info") {
  // Remove any existing messages
  const existingMessage = document.getElementById("loginMessage");
  if (existingMessage) {
    existingMessage.remove();
  }

  // Create message element
  const messageDiv = document.createElement("div");
  messageDiv.id = "loginMessage";
  messageDiv.style.padding = "10px";
  messageDiv.style.margin = "10px 0";
  messageDiv.style.borderRadius = "4px";
  messageDiv.style.textAlign = "center";
  
  switch (type) {
    case "error":
      messageDiv.style.backgroundColor = "#fee";
      messageDiv.style.color = "#c33";
      messageDiv.style.border = "1px solid #c33";
      break;
    case "success":
      messageDiv.style.backgroundColor = "#efe";
      messageDiv.style.color = "#363";
      messageDiv.style.border = "1px solid #363";
      break;
    case "warning":
      messageDiv.style.backgroundColor = "#ffd";
      messageDiv.style.color = "#660";
      messageDiv.style.border = "1px solid #660";
      break;
    default:
      messageDiv.style.backgroundColor = "#eef";
      messageDiv.style.color = "#336";
      messageDiv.style.border = "1px solid #336";
  }
  
  messageDiv.textContent = message;
  
  // Insert message before the login form
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.parentNode.insertBefore(messageDiv, loginForm);
  } else {
    document.body.insertBefore(messageDiv, document.body.firstChild);
  }
  
  // Auto-remove message after 5 seconds
  setTimeout(() => {
    if (messageDiv.parentNode) {
      messageDiv.parentNode.removeChild(messageDiv);
    }
  }, 5000);
}

// Enhanced logout function with better error handling
function handleLogout() {
  const user = firebase.auth().currentUser;
  if (!user) {
    console.log("No user to log out");
    return;
  }

  // Show loading state
  const logoutButton = document.querySelector('.logout a');
  if (logoutButton) {
    const originalText = logoutButton.textContent;
    logoutButton.textContent = "Logging out...";
    logoutButton.style.opacity = "0.7";
  }

  firebase.auth().signOut().then(() => {
    // Send logout event to PHP backend
    fetch("logout.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        uid: user.uid,
        email: user.email
      })
    })
    .then(res => res.json())
    .then(data => {
      console.log("Logout successful:", data.message);
      showMessage("Logged out successfully", "success");
      
      // Redirect after successful logout
      setTimeout(() => {
        window.location.href = "index.php";
      }, 1000);
    })
    .catch((error) => {
      console.error("Logout backend error:", error);
      showMessage("Logged out from Firebase, but backend error occurred", "warning");
    });
  }).catch((error) => {
    console.error("Firebase logout error:", error);
    showMessage("Logout failed. Please try again.", "error");
  }).finally(() => {
    // Restore button state
    const logoutButton = document.querySelector('.logout a');
    if (logoutButton) {
      logoutButton.textContent = "Logout";
      logoutButton.style.opacity = "1";
    }
  });
}
