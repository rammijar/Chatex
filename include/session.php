<?php
// Chatex Social Media Platform
// Session Management File
// Starts session and checks if user is logged in

// Start session (only if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
// If not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
