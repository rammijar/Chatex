<?php
// Chatex Social Media Platform
// Database Configuration File
// This file contains database connection details

// Database credentials - only define if not already defined
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'chatex_db');
}

// Create database connection - only if not already connected
if (!isset($conn) || !$conn) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }
    
    // Set character set to UTF-8
    mysqli_set_charset($conn, "utf8");
}

// Return connection for use in other files
?>
