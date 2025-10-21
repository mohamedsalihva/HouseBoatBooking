<?php
session_start();

require_once __DIR__ . "/inc/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);

    // Fetch user from DB
    $sql    = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role']; // store role in session

            // Redirect based on role
            if ($user['role'] === 'admin') {
                echo "<script>alert('Admin Login Successful'); window.location.href='/HouseBoatBooking/admin/dashboard.php';</script>";
            } else {
                echo "<script>alert('Login Successful'); window.location.href='/HouseBoatBooking/index.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid Password'); window.location.href='../frontend/login/login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found. Please register.'); window.location.href='../frontend/signup/signup.php';</script>";
    }
}
?>
