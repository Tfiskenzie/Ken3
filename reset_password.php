<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = $_SESSION['user_email'];

    if (empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Both fields are required.";
        header('Location: reset_password.php');
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: reset_password.php');
        exit();
    }

    // Hash the new password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_code = NULL, reset_code_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $password_hash, $email);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Your password has been reset successfully. You can now log in.";
        header('Location: loginn.php');
    } else {
        $_SESSION['error'] = "Failed to reset your password. Please try again.";
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="reset_password.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2 class="text-center">Reset Password</h2>
      <p class="text-center">Enter your new password.</p>

      <!-- Display Error/Success Message -->
      <?php
      if (isset($_SESSION['error'])) {
          echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
          unset($_SESSION['error']);
      }
      if (isset($_SESSION['success'])) {
          echo '<div class="success-message">' . htmlspecialchars($_SESSION['success']) . '</div>';
          unset($_SESSION['success']);
      }
      ?>

      <!-- Reset Password Form -->
      <form action="reset_password.php" method="POST">
        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm New Password</label>
          <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <button type="submit" class="btn">Reset Password</button>
      </form>
    </div>
  </div>
</body>
</html>
