<?php
// Chatex Social Media Platform
// Home/Index Page
// Displays newsfeed with posts from friends

include('include/config.php');
include('include/session.php');

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's profile picture
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$profile_pic = $user_data['profile_pic'];

$message = '';

// Create posts folder if not exists
if (!is_dir('posts')) {
    mkdir('posts', 0777, true);
    chmod('posts', 0777);
}

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_post'])) {
    $post_text = trim($_POST['post_text']);
    $post_image = null;
    
    // Validate post
    if (empty($post_text)) {
        $message = "Post cannot be empty";
    } else {
        // Escape post text for SQL
        $post_text = mysqli_real_escape_string($conn, $post_text);
        
        // Handle image upload
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            $filename = $_FILES['post_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                if ($_FILES['post_image']['size'] < 4000000) { // 4MB limit
                    $new_filename = uniqid() . '.' . $ext;
                    $upload_dir = 'posts/' . $new_filename;
                    
                    if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $upload_dir)) {
                        $message = "Failed to upload image. Please try again.";
                    } else {
                        chmod($upload_dir, 0644);
                        $post_image = $new_filename;
                    }
                } else {
                    $message = "Image size must be less than 4MB";
                }
            } else {
                $message = "Only JPG, JPEG, PNG, and GIF files are allowed";
            }
        }
        
        if (empty($message)) {
            // Insert post with or without image
            if ($post_image) {
                $post_image_esc = mysqli_real_escape_string($conn, $post_image);
                $insert_query = "INSERT INTO posts (user_id, post_text, image) VALUES ('$user_id', '$post_text', '$post_image_esc')";
            } else {
                $insert_query = "INSERT INTO posts (user_id, post_text, image) VALUES ('$user_id', '$post_text', NULL)";
            }
            
            if (mysqli_query($conn, $insert_query)) {
                $message = "Post created successfully!";
            } else {
                $message = "Failed to create post: " . mysqli_error($conn);
            }
        }
    }
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];
    
    // Verify post belongs to user
    $verify_query = "SELECT * FROM posts WHERE id = '$post_id' AND user_id = '$user_id'";
    $verify_result = mysqli_query($conn, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        $post = mysqli_fetch_assoc($verify_result);
        
        // Delete image if exists
        if ($post['image']) {
            @unlink('posts/' . $post['image']);
        }
        
        $delete_query = "DELETE FROM posts WHERE id = '$post_id'";
        if (mysqli_query($conn, $delete_query)) {
            $message = "Post deleted successfully!";
        }
    }
}

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_like'])) {
    $post_id = $_POST['post_id'];
    
    // Check if already liked
    $check_like = "SELECT * FROM likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
    $like_result = mysqli_query($conn, $check_like);
    
    if (mysqli_num_rows($like_result) > 0) {
        // Unlike
        $delete_like = "DELETE FROM likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        mysqli_query($conn, $delete_like);
    } else {
        // Like and remove dislike if exists
        $remove_dislike = "DELETE FROM dislikes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        mysqli_query($conn, $remove_dislike);
        
        $insert_like = "INSERT INTO likes (post_id, user_id) VALUES ('$post_id', '$user_id')";
        mysqli_query($conn, $insert_like);
    }
}

// Handle dislike/undislike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_dislike'])) {
    $post_id = $_POST['post_id'];
    
    // Check if already disliked
    $check_dislike = "SELECT * FROM dislikes WHERE post_id = '$post_id' AND user_id = '$user_id'";
    $dislike_result = mysqli_query($conn, $check_dislike);
    
    if (mysqli_num_rows($dislike_result) > 0) {
        // Remove dislike
        $delete_dislike = "DELETE FROM dislikes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        mysqli_query($conn, $delete_dislike);
    } else {
        // Dislike and remove like if exists
        $remove_like = "DELETE FROM likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        mysqli_query($conn, $remove_like);
        
        $insert_dislike = "INSERT INTO dislikes (post_id, user_id) VALUES ('$post_id', '$user_id')";
        mysqli_query($conn, $insert_dislike);
    }
}

// Get all posts (from all users for simplicity, can be modified to show only friends' posts)
$posts_query = "SELECT p.*, u.username, u.profile_pic, 
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                (SELECT COUNT(*) FROM dislikes WHERE post_id = p.id) as dislike_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = '$user_id') as liked_by_user,
                (SELECT COUNT(*) FROM dislikes WHERE post_id = p.id AND user_id = '$user_id') as disliked_by_user
                FROM posts p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.created_date DESC";

$posts_result = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="container">
        <div class="main-content">
            <!-- Create post section -->
            <div class="create-post-section">
                <div class="user-profile-mini">
                    <img src="profile/<?php echo $profile_pic; ?>" alt="Profile" class="profile-pic-small">
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="post-form">
                    <textarea name="post_text" placeholder="What's on your mind?" class="post-textarea" required></textarea>
                    <div style="display: flex; gap: 1rem; margin: 1rem 0; align-items: center;">
                        <input type="file" name="post_image" accept="image/*" style="flex: 1;">
                        <button type="submit" name="create_post" class="btn-primary" style="width: auto; margin: 0;">Post</button>
                    </div>
                </form>
            </div>
            
            <!-- Display message -->
            <?php if (!empty($message)): ?>
                <div class="message-box">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Display posts -->
            <div class="posts-section">
                <?php if (mysqli_num_rows($posts_result) > 0): ?>
                    <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                        <div class="post-card">
                            <!-- Post header with user info -->
                            <div class="post-header">
                                <img src="profile/<?php echo $post['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                                <div class="post-user-info">
                                    <p class="post-username"><?php echo $post['username']; ?></p>
                                    <p class="post-date"><?php echo $post['created_date']; ?></p>
                                </div>
                            </div>
                            
                            <!-- Post content -->
                            <div class="post-content">
                                <?php if (!empty($post['image']) && $post['image'] !== NULL): ?>
                                    <img src="posts/<?php echo htmlspecialchars($post['image']); ?>" alt="Post image" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;" onerror="this.style.display='none';">
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($post['post_text']); ?></p>
                            </div>
                            
                            <!-- Post actions -->
                            <div class="post-actions">
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="toggle_like" class="action-btn <?php echo $post['liked_by_user'] ? 'liked' : ''; ?>">
                                        👍 Like (<?php echo $post['like_count']; ?>)
                                    </button>
                                </form>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="toggle_dislike" class="action-btn <?php echo $post['disliked_by_user'] ? 'disliked' : ''; ?>">
                                        👎 Dislike (<?php echo $post['dislike_count']; ?>)
                                    </button>
                                </form>
                                <a href="view_post.php?id=<?php echo $post['id']; ?>" class="action-btn">
                                    💬 Comments (<?php echo $post['comment_count']; ?>)
                                </a>
                                <?php if (isset($post['user_id']) && $post['user_id'] == $user_id): ?>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                                        <button type="submit" name="delete_post" class="action-btn" style="color: #f44336;" onclick="return confirm('Are you sure you want to delete this post?');">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No posts yet. Be the first to post!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-card">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="friends.php">Find Friends</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
