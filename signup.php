<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db_connection.php'; // Include db.php to handle database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the fields are set and not empty
    if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password']) && !empty($_POST['fullname'])) {
        session_start(); // Start the session here

        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($password !== $confirm_password) {
            echo "<script>
                    alert('Passwords do not match! Please make sure both passwords are the same.');
                    window.history.back();
                  </script>";
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // If user exists, show a SweetAlert notification
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                      Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: 'User already exists! Please use a different email.',
                          confirmButtonText: 'OK'
                      }).then(() => {
                          window.history.back(); // Redirect back to the signup form
                      });
                  </script>";
        } else {
            // If the email is not taken, proceed with signup
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);

            if ($stmt->execute()) {
                // Redirect to the login page after successful signup
                echo "<script>
                          alert('Signup successful! Redirecting to login...');
                          window.location.href = 'login.html';
                      </script>";
                exit(); // Ensure the script stops after the redirect
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
                  Swal.fire({
                      icon: 'error',
                      title: 'Missing Fields',
                      text: 'Please fill in all required fields.',
                      confirmButtonText: 'OK'
                  }).then(() => {
                      window.history.back(); // Redirect back to the signup form
                  });
              </script>";
    }
} else {
    echo "Invalid request method.";
}
?>
