firebase.initializeApp(firebaseConfig);
firebase.auth().signInWithEmailAndPassword(email, password)
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("signInButton").addEventListener("click", loginFunction);
});

const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("loginPassword");

document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("loginPassword");

  togglePassword.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });
});

  // Firebase logout + send to backend logout.php
  function handleLogout() {
    const user = firebase.auth().currentUser;
    if (!user) return;

    // 🔄 Sign out from Firebase
    firebase.auth().signOut().then(() => {
      // 📨 Send logout event to your PHP backend
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
        console.log(data.message);
        // Redirect to login page or show logout screen
        window.location.href = "index.php"; // or any page you want
      });
    }).catch((error) => {
      console.error("Logout error:", error);
    });
  }

