<?php
require_once __DIR__ . "/../inc/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.location.href='../../frontend/signup/signup.php';</script>";
        exit();
    }

    // Check if email already exists
    $email_check = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($email_check);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists. Please login.'); window.location.href='../../frontend/login/login.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database (default role is 'user')
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', 'user')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful. Please login.'); window.location.href='../../frontend/login/login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='../../frontend/signup/signup.php';</script>";
    }
}
?>