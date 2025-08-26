import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

// Firebase configuration
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
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

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
