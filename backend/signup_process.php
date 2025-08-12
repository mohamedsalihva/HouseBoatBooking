<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "houseboat_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name     = $_POST['name'];
$email    = $_POST['email'];
$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

// Password check
if ($password !== $confirm) {
    echo "<script>
            alert('Passwords do not match!');
            window.location.href='../frontend/signup/signup.php';
          </script>";
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    echo "<script>
            alert('Registration Successful!');
            window.location.href='../frontend/login/login.php';
          </script>";
    exit();
} else {
    echo "<script>
            alert('Error: " . $stmt->error . "');
            window.location.href='../frontend/signup/signup.php';
          </script>";
}

$stmt->close();
$conn->close();
?>
