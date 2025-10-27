<?php
// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

include '../../backend/inc/db_connect.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: /HouseBoatBooking/admin/users.php");
    exit();
}

$user_id = $_GET['id'];

// Delete the user record from database (but not admin users)
$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: /HouseBoatBooking/admin/users.php?deleted=1");
exit();
?>