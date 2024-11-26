<?php
session_start();
require 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $country = $_POST['country'];
    $password = $_POST['password']; // Get the raw password

    // Ensure no fields are empty
    if (empty($fullname) || empty($email) || empty($country) || empty($password)) {
        die("Please fill in all fields.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    // Hash the password before storing it
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Email is already registered.");
    }

    // Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, country, user_type, password_hash) VALUES (?, ?, ?, ?, ?)");
    $user_type = 'individual'; // Since this is the individual signup form
    $stmt->bind_param("sssss", $fullname, $email, $country, $user_type, $passwordHash);

    if ($stmt->execute()) {
        echo "Signup successful!";
        header("Location: loginn.php"); // Redirect to login page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
