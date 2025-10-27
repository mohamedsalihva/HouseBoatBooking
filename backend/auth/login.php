<?php
session_start();

require_once __DIR__ . "/../inc/db_connect.php";

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
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['name']; // Use 'name' instead of 'username'
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];
            
            // Determine redirect URL
            $redirect_url = '/HouseBoatBooking/index.php'; // default redirect
            
            // Check if there's a stored redirect URL
            if (isset($_SESSION['redirect_url'])) {
                $redirect_url = $_SESSION['redirect_url'];
                // Clear the redirect URL from session
                unset($_SESSION['redirect_url']);
            }

            // Redirect based on role
            if ($user['role'] === 'admin') {
                // For admin users, always redirect to admin dashboard
                echo "<script>alert('Admin Login Successful'); window.location.href='/HouseBoatBooking/admin/dashboard.php';</script>";
            } else {
                // For regular users, redirect to the original page or homepage
                echo "<script>alert('Login Successful'); window.location.href='" . $redirect_url . "';</script>";
            }
        } else {
            echo "<script>alert('Invalid Password'); window.location.href='../../frontend/login/login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found. Please register.'); window.location.href='../../frontend/signup/signup.php';</script>";
    }
}
?>