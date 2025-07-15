<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Page</title>
  <link type="text/css" rel="stylesheet" href="css/register.css"/>
</head>
<body>
  <div class="wrapper">
    <form id="registerForm" action="#" >  <!-- add the php file in ACTION -->
      <h2>Create An Account</h2>
        <div class="input-field">
        <input type="text" name="firstname" required>
        <label>Enter your First Name</label>
      </div>
      <div class="input-field">
        <input type="text" name="lastname"required>
        <label>Enter your Last Name</label>
      </div> 
       <div class="input-field">
        <input type="text" name="username"required>
        <label>Enter your username</label>
      </div>
      <div class="input-field">
        <input type="text" name="email"required>
        <label>Enter your email</label>
      </div>
      <div class="input-field">
        <input type="password" name="password"required>
        <label>Enter your password</label>
      </div> 
      <div class="input-field">
        <input type="password" name="confirmpassword" required>
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