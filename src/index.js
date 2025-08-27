// Initialize Firebase
const firebaseConfig = {
    apiKey: "AIzaSyDoIAiSnBx8GGNhjQkEB7j1bANx7k2l8dc",
    authDomain: "rizzauthapp.firebaseapp.com",
    projectId: "rizzauthapp",
    storageBucket: "rizzauthapp.appspot.com",
    messagingSenderId: "607508317395",
    appId: "1:607508317395:web:f2f403d10915d6d2ef4026",
    measurementId: "G-2YQFBWK95F"
};

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

// Show loading state with spinner
  const signInButton = document.getElementById("signInButton");
  const originalHTML = signInButton.innerHTML;
  signInButton.innerHTML = '<span class="loader-spinner"></span> Signing in...';
  signInButton.disabled = true;
  signInButton.classList.add('loading');

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
      // Handle login errors with more detailed and user-friendly messages
      let errorMessage = "Login failed. Please check your credentials and try again.";
      let errorDetails = "";
      
      switch (error.code) {
        case "auth/invalid-email":
          errorMessage = "Invalid email address format.";
          errorDetails = "Please enter a valid email address (e.g., user@example.com).";
          break;
        case "auth/user-disabled":
          errorMessage = "Account disabled.";
          errorDetails = "This account has been temporarily disabled. Please contact support.";
          break;
        case "auth/user-not-found":
          errorMessage = "Account not found.";
          errorDetails = "No account exists with this email address. Please check or sign up.";
          break;
        case "auth/wrong-password":
          errorMessage = "Incorrect password.";
          errorDetails = "The password you entered is incorrect. Please try again or reset your password.";
          break;
        case "auth/too-many-requests":
          errorMessage = "Too many attempts.";
          errorDetails = "Too many failed login attempts. Please try again in a few minutes.";
          break;
        case "auth/network-request-failed":
          errorMessage = "Network error.";
          errorDetails = "Unable to connect to the server. Please check your internet connection.";
          break;
        default:
          errorDetails = "An unexpected error occurred. Please try again later.";
      }
      
      // Show detailed error message
      showMessage(`${errorMessage} ${errorDetails}`, "error");
      
      // Log detailed error for debugging
      console.error("Login error:", error.code, error.message);
    })
  .finally(() => {
    // Restore button state
    if (signInButton) {
      signInButton.innerHTML = originalHTML;
      signInButton.disabled = false;
      signInButton.classList.remove('loading');
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
  messageDiv.className = type;
  
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

  // Show loading state with spinner
  const logoutButton = document.querySelector('.logout a');
  if (logoutButton) {
    const originalHTML = logoutButton.innerHTML;
    logoutButton.innerHTML = '<span class="loader-spinner"></span> Logging out...';
    logoutButton.style.opacity = "0.7";
    logoutButton.classList.add('loading');
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
      logoutButton.innerHTML = originalHTML || "Logout";
      logoutButton.style.opacity = "1";
      logoutButton.classList.remove('loading');
    }
  });
}
