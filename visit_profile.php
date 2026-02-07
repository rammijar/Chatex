<?php
// Chatex Social Media Platform
// Visit Other User's Profile Page
// Allows viewing other users' profiles and sending friend requests

include('include/config.php');
include('include/session.php');

$current_user_id = $_SESSION['user_id'];
$profile_user_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$profile_user_id || $profile_user_id == $current_user_id) {
    header("Location: profile.php");
    exit();
}

// Get profile user data
$user_query = "SELECT * FROM users WHERE id = '$profile_user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header("Location: index.php");
    exit();
}

// Check if they are friends
$friend_query = "SELECT * FROM friends WHERE (user_id_1 = '$current_user_id' AND user_id_2 = '$profile_user_id') 
                 OR (user_id_1 = '$profile_user_id' AND user_id_2 = '$current_user_id')";
$friend_result = mysqli_query($conn, $friend_query);
$is_friend = mysqli_num_rows($friend_result) > 0;

// Check if friend request is pending
$pending_query = "SELECT * FROM friend_requests WHERE requester_id = '$current_user_id' AND receiver_id = '$profile_user_id' AND status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
$has_pending_request = mysqli_num_rows($pending_result) > 0;

$message = '';

// Handle friend request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_request'])) {
    $insert_query = "INSERT INTO friend_requests (requester_id, receiver_id, status) VALUES ('$current_user_id', '$profile_user_id', 'pending')";
    
    if (mysqli_query($conn, $insert_query)) {
        $message = "Friend request sent!";
        $has_pending_request = true;
    }
}

// Get user's posts
$posts_query = "SELECT p.*, 
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                FROM posts p
                WHERE p.user_id = '$profile_user_id'
                ORDER BY p.created_date DESC";
$posts_result = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user['username']; ?>'s Profile - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="container">
        <div class="main-content">
            <!-- Back button -->
            <a href="friends.php" class="back-link">← Back to Friends</a>
            
            <!-- Profile card -->
            <div class="profile-card">
                <div class="profile-header">
                    <img src="profile/<?php echo $user['profile_pic']; ?>" alt="Profile" class="profile-pic-large">
                    <div class="profile-header-info">
                        <h2><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h2>
                        <p class="username">@<?php echo $user['username']; ?></p>
                        <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet'); ?></p>
                        <p class="join-date">Joined: <?php echo date('F d, Y', strtotime($user['registration_date'])); ?></p>
                        <p class="online-status"><?php echo $user['online_status'] ? '🟢 Online' : '🔴 Offline'; ?></p>
                    </div>
                    
                    <!-- Action buttons -->
                    <div class="profile-actions">
                        <?php if ($is_friend): ?>
                            <a href="messages.php?user_id=<?php echo $profile_user_id; ?>" class="btn-primary">Send Message</a>
                        <?php elseif ($has_pending_request): ?>
                            <button class="btn-primary" disabled>Request Sent</button>
                        <?php else: ?>
                            <form method="POST" class="inline-form">
                                <button type="submit" name="send_request" class="btn-primary">Add Friend</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message-box">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>
            
            <!-- User's posts -->
            <div class="user-posts-section">
                <h3><?php echo $user['username']; ?>'s Posts</h3>
                <?php if (mysqli_num_rows($posts_result) > 0): ?>
                    <div class="posts-section">
                        <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                            <div class="post-card">
                                <div class="post-content">
                                    <p><?php echo htmlspecialchars($post['post_text']); ?></p>
                                </div>
                                <div class="post-actions">
                                    <a href="view_post.php?id=<?php echo $post['id']; ?>" class="action-btn">
                                        View Post
                                    </a>
                                    <span>👍 <?php echo $post['like_count']; ?> Likes</span>
                                    <span>💬 <?php echo $post['comment_count']; ?> Comments</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-text">No posts yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
