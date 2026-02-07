<?php
// Chatex Social Media Platform
// Logout File
// Logs out the user and destroys session

include('include/config.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get user ID before destroying session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Update offline status
    $query = "UPDATE users SET online_status = 0 WHERE id = '$user_id'";
    mysqli_query($conn, $query);
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
