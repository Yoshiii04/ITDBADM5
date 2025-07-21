<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Page</title>
  <link type="text/css" rel="stylesheet" href="css/register.css"/>
</head>
<body>

  <?php 
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Show errors for debugging (disable in production)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "online_store";

    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        // Return an error message as plain text
        echo "Connection failed: " . $conn->connect_error;
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $userpassword = $_POST['password'] ?? '';
        $confirmpassword = $_POST['confirmpassword'] ?? '';

        // Basic validation
        if (!$username || !$email || !$userpassword || !$confirmpassword) {
            echo "Please fill in all fields.";
            exit;
        }

        if ($userpassword !== $confirmpassword) {
            echo "Passwords do not match.";
            exit;
        }

        $password_hash = password_hash($userpassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, role, password_hash) VALUES (?, ?, 'customer', ?)");
        if (!$stmt) {
            echo "Database error: " . $conn->error;
            exit;
        }
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Registration error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid request method.";
    }
    $conn->close();
  ?>

  <div class="wrapper">
    <form id="registerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <h2>Create An Account</h2>
       <div class="input-field">
        <input type="text" id="username" name="username" required>
        <label>Enter your username</label>
      </div>
      <div class="input-field">
        <input type="text" id="email" name="email" required>
        <label>Enter your email</label>
      </div>
      <div class="input-field">
        <input type="password" id="userpassword" name="password" required>
        <label>Enter your password</label>
      </div> 
      <div class="input-field">
        <input type="password" id="confirmpassword" name="confirmpassword" required>
        <label>Confirm your password</label>
      </div>
      <button type="submit">Register</button>
      <div class="login">
        <p>Already have an account? <a href="login.html">Login</a></p>
      </div>
    </form>
  </div>
  <script src="js/register.js"></script>
</body>
</html>

