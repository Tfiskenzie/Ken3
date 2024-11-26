<?php
session_start();
include 'db_connection.php'; // Include your database connection file

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
 // Autoload PHPMailer via Composer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['error'] = "Please enter your email address.";
        header('Location: forgot_password.php');
        exit();
    }

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Generate a confirmation code
        $code = rand(100000, 999999);

        // Save the code to the database
        $stmt = $conn->prepare("UPDATE users SET reset_code = ?, reset_code_expiry = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?");
        $stmt->bind_param("ii", $code, $user_id);
        $stmt->execute();

        // Send the code to the user's email using Mailgun
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.mailgun.org'; // Mailgun SMTP hostname
            $mail->SMTPAuth = true;
            $mail->Username = 'postmaster@sandboxe5a7cb09255f4eaaaaa7d6e0457dc6ae.mailgun.org'; // Mailgun username
            $mail->Password = '146e734667b93a572a60494268cbd3ae-c02fd0ba-c6941a43'; // Mailgun password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Settings
            $mail->setFrom('no-reply@yourdomain.com', 'Your App Name'); // Replace with your sender email and name
            $mail->addAddress($email); // Recipient email
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your password reset code is: $code\nThis code is valid for 15 minutes.";

            // Send Email
            if ($mail->send()) {
                $_SESSION['success'] = "A confirmation code has been sent to your email.";
                $_SESSION['user_email'] = $email; // Save email for the next step
                header('Location: verify_code.php');
                exit();
            } else {
                throw new Exception("Failed to send the confirmation code.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send the confirmation code. Error: " . $mail->ErrorInfo;
            header('Location: forgot_password.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "No account found with this email.";
    }

    header('Location: forgot_password.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="forgot_password.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2 class="text-center">Forgot Password</h2>
      <p class="text-center">Enter your email to receive a reset code.</p>

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

      <!-- Forgot Password Form -->
      <form action="forgot_password.php" method="POST">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" required>
        </div>
        <button type="submit" class="btn">Send Code</button>
      </form>
    </div>
  </div>
</body>
</html>
