<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $email = $_SESSION['user_email'];

    if (empty($code)) {
        $_SESSION['error'] = "Please enter the reset code.";
        header('Location: verify_code.php');
        exit();
    }

    // Verify the reset code
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_code = ? AND reset_code_expiry > NOW()");
    $stmt->bind_param("si", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['success'] = "Code verified. You can now reset your password.";
        header('Location: reset_password.php');
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired reset code.";
    }

    header('Location: verify_code.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Code</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="verify_code.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2 class="text-center">Verify Code</h2>
      <p class="text-center">Enter the reset code sent to your email.</p>

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

      <!-- Verify Code Form -->
      <form action="verify_code.php" method="POST">
        <div class="form-group">
          <label for="code">Reset Code</label>
          <input type="text" name="code" id="code" required>
        </div>
        <button type="submit" class="btn">Verify Code</button>
      </form>
    </div>
  </div>
</body>
</html>
