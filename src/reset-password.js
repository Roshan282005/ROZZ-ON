// Password reset functionality with Firebase
document.addEventListener("DOMContentLoaded", function() {
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
    firebase.initializeApp(firebaseConfig);

    const resetPasswordForm = document.getElementById("resetPasswordForm");
    const resetMessage = document.getElementById("resetMessage");
    
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener("submit", async function(e) {
            e.preventDefault();
            const email = document.getElementById("resetEmail").value;
            
            try {
                await firebase.auth().sendPasswordResetEmail(email);
                resetMessage.textContent = "Password reset email sent! Check your inbox.";
                resetMessage.style.color = "green";
            } catch (error) {
                resetMessage.textContent = "Error: " + error.message;
                resetMessage.style.color = "red";
            }
        });
    }
});

// Function to handle password reset
async function handlePasswordReset(email) {
    try {
        await firebase.auth().sendPasswordResetEmail(email);
        return { success: true, message: "Password reset email sent!" };
    } catch (error) {
        return { success: false, message: error.message };
    }
}
