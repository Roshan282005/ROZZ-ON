// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "firebase/auth";

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyDoIAiSnBx8GGNhjQkEB7j1bANx7k2l8dc",
  authDomain: "rizzauthapp.firebaseapp.com",
  databaseURL: "https://rizzauthapp-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "rizzauthapp",
  storageBucket: "rizzauthapp.firebasestorage.app",
  messagingSenderId: "607508317395",
  appId: "1:607508317395:web:f2f403d10915d6d2ef4026",
  measurementId: "G-2YQFBWK95F"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

const hamMenu = document.querySelector(".ham-menu");
const offScreenMenu = document.querySelector(".off-screen-menu");

// Toggle menu on hamburger click
hamMenu.addEventListener("click", (e) => {
  e.stopPropagation();
  hamMenu.classList.toggle("active");
  offScreenMenu.classList.toggle("active");
});

// Close menu when clicking outside
document.addEventListener("click", (e) => {
  const isClickInsideMenu = offScreenMenu.contains(e.target);
  const isClickOnHamMenu = hamMenu.contains(e.target);
  
  if (!isClickInsideMenu && !isClickOnHamMenu && offScreenMenu.classList.contains("active")) {
    hamMenu.classList.remove("active");
    offScreenMenu.classList.remove("active");
  }
});

// Close menu with ESC key
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && offScreenMenu.classList.contains("active")) {
    hamMenu.classList.remove("active");
    offScreenMenu.classList.remove("active");
  }
});

// Close menu when a menu item is clicked (for single page applications)
const menuItems = document.querySelectorAll(".off-screen-menu a");
menuItems.forEach(item => {
  item.addEventListener("click", () => {
    hamMenu.classList.remove("active");
    offScreenMenu.classList.remove("active");
  });
});

// Google Sign-In button handler
const googleSignInBtn = document.getElementById("googleSignInBtn");
const googleLoginBtn = document.getElementById("googleLoginBtn");

const handleGoogleLogin = () => {
  signInWithPopup(auth, provider)
    .then((result) => {
      const user = result.user;
      // Send user info to backend as JSON
      const userData = {
        uid: user.uid,
        email: user.email,
        name: user.displayName || "Google User"
      };

      return fetch("http://localhost/rizz/login/track-login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(userData)
      });
    })
    .then((response) => {
      if (response.ok) {
        alert(`Welcome, ${user.displayName} (${user.email})`);
        window.location.href = "index.html";
      } else {
        throw new Error("Failed to save user data");
      }
    })
    .catch((error) => {
      alert(`Google login failed: ${error.message}`);
      console.error("Google login error:", error);
    });
};

if (googleSignInBtn) {
  googleSignInBtn.addEventListener("click", handleGoogleLogin);
}

if (googleLoginBtn) {
  googleLoginBtn.addEventListener("click", handleGoogleLogin);
}
