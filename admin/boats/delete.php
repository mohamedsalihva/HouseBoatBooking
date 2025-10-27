<?php
session_start();
include '../../backend/inc/db_connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

// Check if boat ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: /HouseBoatBooking/admin/boats.php");
    exit();
}

$boat_id = intval($_GET['id']);

// First, fetch the boat to get image information
$stmt = $conn->prepare("SELECT image FROM boats WHERE id = ?");
$stmt->bind_param("i", $boat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $boat = $result->fetch_assoc();
    
    // Delete images from server
    if (!empty($boat['image'])) {
        $images = json_decode($boat['image'], true);
        if (is_array($images)) {
            foreach ($images as $image) {
                $file_path = '../../uploads/boats/' . $image;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        } else {
            // Single image (old format)
            $file_path = '../../uploads/boats/' . $boat['image'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
    
    // Delete boat from database
    $stmt = $conn->prepare("DELETE FROM boats WHERE id = ?");
    $stmt->bind_param("i", $boat_id);
    
    if ($stmt->execute()) {
        header("Location: /HouseBoatBooking/admin/boats.php?deleted=1");
    } else {
        header("Location: /HouseBoatBooking/admin/boats.php?error=1");
    }
} else {
    header("Location: /HouseBoatBooking/admin/boats.php?error=1");
}

exit();
?>