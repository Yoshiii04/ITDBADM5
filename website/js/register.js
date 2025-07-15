document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // prevent default form submission

    const formData = new FormData(form);
    const email = formData.get("email");
    const password = formData.get("password");
    const confirmPassword = formData.get("confirmpassword");

    // Frontend validation
    if (!email || !password || !confirmPassword) {
      alert("Please fill in all required fields.");
      return;
    }

    if (password !== confirmPassword) {
      alert("Passwords do not match.");
      return;
    }

// Submit data to backend
    /*   
    fetch("register.php", {
    method: "POST",
    body: formData
    })
    .then(res => res.text())
    .then(data => {
    if (data === "success") {
        alert("Registration successful!");
        window.location.href = "login.html";
    } else {
        alert("Registration failed: " + data);
    }
    })
    .catch(error => {
    console.error("Error:", error);
    alert("An error occurred. Please try again.");
    });
    */ 

// Simulate backend response (for testing only)
    setTimeout(() => {
    alert("Pretend registration was successful.");
    window.location.href = "login.php";
    }, 1000);


});
});
