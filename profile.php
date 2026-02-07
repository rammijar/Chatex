<?php
// Chatex Social Media Platform
// User Profile Page
// Allows users to view and edit their profile

include('include/config.php');
include('include/session.php');

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user data
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    
    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['profile_pic']['size'] < 2000000) {
            $new_filename = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'profile/' . $new_filename)) {
                $user['profile_pic'] = $new_filename;
            }
        }
    }
    
    // Update database
    $update_query = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', email = '$email', phone = '$phone', bio = '$bio', profile_pic = '" . $user['profile_pic'] . "' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $message = "Failed to update profile";
    }
}

// Get user's posts
$posts_query = "SELECT p.*, 
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                FROM posts p
                WHERE p.user_id = '$user_id'
                ORDER BY p.created_date DESC";
$posts_result = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="container">
        <div class="main-content">
            <!-- Display message -->
            <?php if (!empty($message)): ?>
                <div class="message-box">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Profile card -->
            <div class="profile-card">
                <div class="profile-header">
                    <img src="profile/<?php echo $user['profile_pic']; ?>" alt="Profile" class="profile-pic-large">
                    <div class="profile-header-info">
                        <h2><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h2>
                        <p class="username">@<?php echo $user['username']; ?></p>
                        <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet'); ?></p>
                        <p class="join-date">Joined: <?php echo date('F d, Y', strtotime($user['registration_date'])); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Edit profile form -->
            <div class="edit-profile-card">
                <h3>Edit Profile</h3>
                <form method="POST" enctype="multipart/form-data" class="profile-form">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstname" value="<?php echo $user['firstname']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastname" value="<?php echo $user['lastname']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo $user['phone']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" placeholder="Write something about yourself" class="profile-textarea"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_pic" accept="image/*">
                        <small>Leave empty to keep current picture</small>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-primary">Save Changes</button>
                </form>
            </div>
            
            <!-- User's posts -->
            <div class="user-posts-section">
                <h3>My Posts</h3>
                <?php if (mysqli_num_rows($posts_result) > 0): ?>
                    <div class="posts-section">
                        <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                            <div class="post-card">
                                <div class="post-content">
                                    <p><?php echo htmlspecialchars($post['post_text']); ?></p>
                                </div>
                                <div class="post-actions">
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
