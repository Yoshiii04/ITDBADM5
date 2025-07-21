document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("register.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      data = data.trim(); // clean up any whitespace
      if (data === "success") {
        alert("Registration successful.");
        window.location.href = "login.php";
      } else {
        alert("Registration failed: " + data);
      }
    })
    .catch(err => {
      alert("An error occurred: " + err.message);
    });
  });
});



  // // Simulate backend response (for testing only)
  // setTimeout(() => {
  //   alert("Registration was successful.");
  //   window.location.href = "login.php";
  //   }, 500);
