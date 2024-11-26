<?php
// Start session
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirect to dashboard if the user is already logged in
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="login.css">
  <style>
    body 
  </style>
</head>
<body>



  <div class="container">
    <div class="card">
     <a href="event.html">&#x2196;</a><h2 class="text-center">Welcome Back</h2>
      <p class="text-center">Enter your credentials to continue.</p>

      <!-- Display Error Message -->
      <?php
      if (isset($_SESSION['error'])) {
          echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
          unset($_SESSION['error']); // Clear the error after displaying
      }
      ?>

      <!-- Login Form -->
      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="email">Email<i id="terick">**</i></label>
          <input type="email" name="email" id="email" placeholder="" required>
        </div>
        <div class="form-group">
          <label for="password">Password<i id="terick">**</i></label>
          <input type="password" name="password" id="password" placeholder="" required autocomplete="off">
        </div>
        <button type="submit" class="btn">Sign In</button>
      </form>

      <div class="text-center">
        <a href="forgot_password.php">Forgot password?</a>
      </div>
      <p class="text-center">
        Don't have an account? <a href="Signup.html">Sign up</a>
      </p>
    </div>
    </div>
</body>
</html>
