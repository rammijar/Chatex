<?php
// Chatex Social Media Platform
// View Post Page
// Displays a single post with comments

include('include/config.php');
include('include/session.php');

$user_id = $_SESSION['user_id'];
$post_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$post_id) {
    header("Location: index.php");
    exit();
}

$message = '';

// Get post details
$post_query = "SELECT p.*, u.username, u.profile_pic, 
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
               (SELECT COUNT(*) FROM dislikes WHERE post_id = p.id) as dislike_count,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = '$user_id') as liked_by_user,
               (SELECT COUNT(*) FROM dislikes WHERE post_id = p.id AND user_id = '$user_id') as disliked_by_user
               FROM posts p
               JOIN users u ON p.user_id = u.id
               WHERE p.id = '$post_id'";
$post_result = mysqli_query($conn, $post_query);
$post = mysqli_fetch_assoc($post_result);

if (!$post) {
    header("Location: index.php");
    exit();
}

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])) {
    $comment_text = trim($_POST['comment_text']);
    
    if (!empty($comment_text)) {
        $insert_query = "INSERT INTO comments (post_id, user_id, comment_text) VALUES ('$post_id', '$user_id', '$comment_text')";
        
        if (mysqli_query($conn, $insert_query)) {
            $message = "Comment added!";
        }
    }
}

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_like'])) {
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
    
    // Refresh post data
    $post_result = mysqli_query($conn, $post_query);
    $post = mysqli_fetch_assoc($post_result);
}

// Handle dislike/undislike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_dislike'])) {
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
    
    // Refresh post data
    $post_result = mysqli_query($conn, $post_query);
    $post = mysqli_fetch_assoc($post_result);
}

// Handle delete post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    // Verify post belongs to user
    $verify_query = "SELECT * FROM posts WHERE id = '$post_id' AND user_id = '$user_id'";
    $verify_result = mysqli_query($conn, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        $post_data = mysqli_fetch_assoc($verify_result);
        
        // Delete image if exists
        if ($post_data['image']) {
            @unlink('posts/' . $post_data['image']);
        }
        
        $delete_query = "DELETE FROM posts WHERE id = '$post_id'";
        if (mysqli_query($conn, $delete_query)) {
            header("Location: index.php");
            exit();
        }
    }
}

// Handle delete comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    
    // Verify comment belongs to user
    $verify_query = "SELECT * FROM comments WHERE id = '$comment_id' AND user_id = '$user_id'";
    $verify_result = mysqli_query($conn, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        $delete_query = "DELETE FROM comments WHERE id = '$comment_id'";
        mysqli_query($conn, $delete_query);
    }
}

// Get comments
$comments_query = "SELECT c.*, u.username, u.profile_pic 
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = '$post_id'
                  ORDER BY c.created_date ASC";
$comments_result = mysqli_query($conn, $comments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="container">
        <div class="main-content">
            <!-- Back button -->
            <a href="index.php" class="back-link">← Back to Home</a>
            
            <!-- Post -->
            <div class="post-card full-post">
                <div class="post-header">
                    <img src="profile/<?php echo $post['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                    <div class="post-user-info">
                        <a href="visit_profile.php?id=<?php echo $post['user_id']; ?>" class="post-username"><?php echo $post['username']; ?></a>
                        <p class="post-date"><?php echo $post['created_date']; ?></p>
                    </div>
                    <?php if (isset($post['user_id']) && $post['user_id'] == $user_id): ?>
                        <form method="POST" style="margin-left: auto;">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                            <button type="submit" name="delete_post" class="action-btn" style="color: #f44336;" onclick="return confirm('Are you sure you want to delete this post?');">
                                🗑️ Delete
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="post-content">
                    <?php if (!empty($post['image']) && $post['image'] !== NULL): ?>
                        <img src="posts/<?php echo htmlspecialchars($post['image']); ?>" alt="Post image" style="width: 100%; max-height: 500px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;" onerror="this.style.display='none';">
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($post['post_text']); ?></p>
                </div>
                
                <div class="post-actions">
                    <form method="POST" class="inline-form">
                        <button type="submit" name="toggle_like" class="action-btn <?php echo $post['liked_by_user'] ? 'liked' : ''; ?>">
                            👍 Like (<?php echo $post['like_count']; ?>)
                        </button>
                    </form>
                    <form method="POST" class="inline-form">
                        <button type="submit" name="toggle_dislike" class="action-btn <?php echo $post['disliked_by_user'] ? 'disliked' : ''; ?>">
                            👎 Dislike (<?php echo $post['dislike_count']; ?>)
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Add comment section -->
            <div class="comment-section">
                <h3>Comments</h3>
                
                <?php if (!empty($message)): ?>
                    <div class="message-box">
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="comment-form">
                    <textarea name="comment_text" placeholder="Write a comment..." class="comment-input" required></textarea>
                    <button type="submit" name="add_comment" class="btn-primary">Post Comment</button>
                </form>
                
                <!-- Display comments -->
                <div class="comments-list">
                    <?php if (mysqli_num_rows($comments_result) > 0): ?>
                        <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                            <div class="comment-item">
                                <img src="profile/<?php echo $comment['profile_pic']; ?>" alt="Profile" class="profile-pic-tiny">
                                <div class="comment-content">
                                    <p class="comment-author"><?php echo $comment['username']; ?></p>
                                    <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                    <small><?php echo $comment['created_date']; ?></small>
                                </div>
                                <?php if ($comment['user_id'] == $user_id): ?>
                                    <form method="POST" style="margin-left: auto;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <button type="submit" name="delete_comment" class="action-btn" style="color: #f44336; white-space: nowrap;" onclick="return confirm('Delete this comment?');">
                                            🗑️
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-text">No comments yet. Be the first!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
