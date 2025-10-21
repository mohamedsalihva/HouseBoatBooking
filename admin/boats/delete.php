<?php
include '../../backend/inc/db_connect.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../boats.php");
    exit();
}

$boat_id = $_GET['id'];

// First, get the image name to delete the file
$stmt = $conn->prepare("SELECT image FROM boats WHERE id = ?");
$stmt->bind_param("i", $boat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $boat = $result->fetch_assoc();
    
    // Delete the image file if it exists
    if (!empty($boat['image']) && file_exists("../../uploads/boats/" . $boat['image'])) {
        unlink("../../uploads/boats/" . $boat['image']);
    }
    
    // Delete the boat record from database
    $stmt = $conn->prepare("DELETE FROM boats WHERE id = ?");
    $stmt->bind_param("i", $boat_id);
    $stmt->execute();
}

header("Location: ../boats.php");
exit();
?>