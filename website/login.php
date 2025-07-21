<?php
session_start();

// Self reference
$currentPage = basename($_SERVER['PHP_SELF']);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

// Create & check connection

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password_hash'];

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Check password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, start a session
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php"); // Redirect to dashboard or home page
        } else {
            // Incorrect password
            $error_message = "Invalid password.";
        }
    } else {
        // Username not found
        $error_message = "No user found with that username.";
    }
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link type="text/css" rel="stylesheet" href="css/login.css"/>
</head>
<body>

  <div class="wrapper">
      <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>
    <form id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <h2>Login</h2>
        <div class="input-field">
        <input type="text" name="username" required>
        <label>Enter your username</label>
      </div>
      <div class="input-field">
        <input type="password" name="password" required>
        <label>Enter your password</label>
      </div>
      <div class="forget">
        <label for="remember">
          <input type="checkbox" id="remember">
          <p>Remember me</p>
        </label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="register.php">Register</a></p>
      </div>
    </form>
  </div>
  <script src="js/login.js"></script>
</body>
</html>