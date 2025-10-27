<?php
// Start session with proper configuration
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the correct logout page
header("Location: ../../backend/auth/logout.php");
exit();
?>