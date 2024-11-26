<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND confirmation_code = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mark user as verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, confirmation_code = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        echo "Email verified successfully! You can now log in.";
        header("Location: login.html");
    } else {
        echo "Invalid confirmation code.";
    }
}
?>
