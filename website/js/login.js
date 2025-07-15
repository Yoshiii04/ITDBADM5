document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.querySelector("input[name='email']").value.trim();
  const password = document.querySelector("input[name='password']").value;

  if (!email || !password) {
    alert("Please fill in both fields.");
    return;
  }

  const formData = new FormData(this);

  // Simulate backend response (replace with fetch later)
  setTimeout(() => {
    alert("Login successful (mock)");
    window.location.href = "index.php"; // Redirect to homepage/dashboard        -- CTRL + SHIFT + R do refresh ! -- 
  }, 1000);

   //Real fetch if backend exists:
  /*
  
  fetch("login.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    if (data === "success") {
      window.location.href = "dashboard.html";
    } else {
      alert("Login failed: " + data);
    }
  })
  .catch(error => console.error("Error:", error));
  */

});
