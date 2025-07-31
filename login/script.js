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

