<?php
// Chatex Social Media Platform
// Header Navigation File
// Contains common navbar for all pages

include('config.php');

// Get current user info if logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    // Count unread messages
    $unread_query = "SELECT COUNT(*) as unread FROM messages WHERE receiver_id = '$user_id' AND is_read = 0";
    $unread_result = mysqli_query($conn, $unread_query);
    $unread_data = mysqli_fetch_assoc($unread_result);
    $unread_count = $unread_data['unread'];
}
?>

<!-- Navigation Bar -->
<nav class="navbar">
    <style>
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            border: 2px solid white;
        }
        
        .nav-item-wrapper {
            position: relative;
            display: inline-block;
        }
    </style>
    
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php">Chatex</a>
        </div>
        
        <div class="nav-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php">Home</a>
                <a href="friends.php">Friends</a>
                <div class="nav-item-wrapper">
                    <a href="messages.php">Messages</a>
                    <?php if (isset($unread_count) && $unread_count > 0): ?>
                        <span class="notification-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </div>
                <a href="profile.php">Profile</a>
                <?php if ($user['username'] == 'admin'): ?>
                    <a href="admin.php" style="color: #ff9800; font-weight: bold;">⚙️ Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
