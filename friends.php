<?php
// Chatex Social Media Platform
// Friends Management Page
// Shows friend requests and allows adding/accepting friends

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

// Handle sending friend request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_request'])) {
    $receiver_id = $_POST['receiver_id'];
    
    // Check if PENDING request already exists
    $check_query = "SELECT * FROM friend_requests WHERE requester_id = '$user_id' AND receiver_id = '$receiver_id' AND status = 'pending'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $message = "Friend request already sent";
    } else {
        // Send friend request
        $insert_query = "INSERT INTO friend_requests (requester_id, receiver_id, status) VALUES ('$user_id', '$receiver_id', 'pending')";
        
        if (mysqli_query($conn, $insert_query)) {
            $message = "Friend request sent!";
        }
    }
}

// Handle accepting friend request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_request'])) {
    $requester_id = $_POST['requester_id'];
    
    // Update friend request status
    $update_query = "UPDATE friend_requests SET status = 'accepted' WHERE requester_id = '$requester_id' AND receiver_id = '$user_id'";
    mysqli_query($conn, $update_query);
    
    // Add to friends table
    $insert_friend = "INSERT INTO friends (user_id_1, user_id_2) VALUES ('$requester_id', '$user_id')";
    mysqli_query($conn, $insert_friend);
    
    $message = "Friend request accepted!";
}

// Handle rejecting friend request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_request'])) {
    $requester_id = $_POST['requester_id'];
    
    // Update friend request status
    $delete_query = "DELETE FROM friend_requests WHERE requester_id = '$requester_id' AND receiver_id = '$user_id'";
    mysqli_query($conn, $delete_query);
    
    $message = "Friend request rejected";
}

// Handle unfriend
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unfriend'])) {
    $friend_id = $_POST['friend_id'];
    
    // Remove from friends table
    $delete_query = "DELETE FROM friends WHERE (user_id_1 = '$user_id' AND user_id_2 = '$friend_id') OR (user_id_1 = '$friend_id' AND user_id_2 = '$user_id')";
    mysqli_query($conn, $delete_query);
    
    // Delete all messages between the two users
    $delete_messages = "DELETE FROM messages WHERE (sender_id = '$user_id' AND receiver_id = '$friend_id') OR (sender_id = '$friend_id' AND receiver_id = '$user_id')";
    mysqli_query($conn, $delete_messages);
    
    $message = "Friend removed and conversation cleared";
}

// Handle search for users
$search_query = '';
$search_results = null;
$has_searched = false;

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $has_searched = true;
    $search_query = mysqli_real_escape_string($conn, trim($_GET['search']));
    
    // Search for users by username, firstname, or lastname
    $search_results_query = "SELECT u.* FROM users u 
                            WHERE u.id != '$user_id' 
                            AND (u.username LIKE '%$search_query%' OR u.firstname LIKE '%$search_query%' OR u.lastname LIKE '%$search_query%')
                            AND u.id NOT IN (SELECT user_id_1 FROM friends WHERE user_id_2 = '$user_id')
                            AND u.id NOT IN (SELECT user_id_2 FROM friends WHERE user_id_1 = '$user_id')
                            AND u.id NOT IN (SELECT requester_id FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending')
                            AND u.id NOT IN (SELECT receiver_id FROM friend_requests WHERE requester_id = '$user_id' AND status = 'pending')
                            LIMIT 10";
    $search_results = mysqli_query($conn, $search_results_query);
}

// Get list of friends
$friends_query = "SELECT u.* FROM users u
                  JOIN friends f ON (f.user_id_1 = u.id OR f.user_id_2 = u.id)
                  WHERE (f.user_id_1 = '$user_id' OR f.user_id_2 = '$user_id') AND u.id != '$user_id'";
$friends_result = mysqli_query($conn, $friends_query);

// Get pending friend requests
$requests_query = "SELECT u.* FROM users u
                   WHERE u.id IN (SELECT requester_id FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending')";
$requests_result = mysqli_query($conn, $requests_query);

// Get suggested users (users not yet friends with)
$suggested_query = "SELECT u.* FROM users u 
                    WHERE u.id != '$user_id' 
                    AND u.id NOT IN (SELECT user_id_1 FROM friends WHERE user_id_2 = '$user_id')
                    AND u.id NOT IN (SELECT user_id_2 FROM friends WHERE user_id_1 = '$user_id')
                    AND u.id NOT IN (SELECT requester_id FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending')
                    AND u.id NOT IN (SELECT receiver_id FROM friend_requests WHERE requester_id = '$user_id' AND status = 'pending')
                    LIMIT 5";
$suggested_result = mysqli_query($conn, $suggested_query);

// Get sent friend requests (pending requests FROM current user)
$sent_requests_query = "SELECT u.* FROM users u 
                        WHERE u.id IN (SELECT receiver_id FROM friend_requests WHERE requester_id = '$user_id' AND status = 'pending')";
$sent_requests_result = mysqli_query($conn, $sent_requests_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends - Chatex</title>
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
            
            <!-- Search Users Section -->
            <div class="section-card search-section">
                <h2>🔍 Search Users</h2>
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search by username or name..." value="<?php echo htmlspecialchars($search_query); ?>" class="search-input">
                    <button type="submit" class="btn-primary" style="width: auto;">Search</button>
                </form>
                
                <!-- Display search results -->
                <?php if ($has_searched): ?>
                    <div style="margin-top: 1.5rem;">
                        <?php if ($search_results && mysqli_num_rows($search_results) > 0): ?>
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">Found <?php echo mysqli_num_rows($search_results); ?> user(s)</p>
                            <div class="user-list">
                                <?php while ($user = mysqli_fetch_assoc($search_results)): ?>
                                    <div class="user-item">
                                        <div class="user-info">
                                            <img src="profile/<?php echo $user['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                                            <div>
                                                <p class="username"><?php echo $user['username']; ?></p>
                                                <p class="user-name"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></p>
                                            </div>
                                        </div>
                                        <div class="user-actions">
                                            <form method="POST" class="inline-form">
                                                <input type="hidden" name="receiver_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="send_request" class="btn-small btn-primary">Add Friend</button>
                                            </form>
                                            <a href="visit_profile.php?id=<?php echo $user['id']; ?>" class="btn-small btn-secondary" style="margin-left: 0.5rem;">View Profile</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-text" style="text-align: center; padding: 2rem; color: #999;">
                                <p>No users found matching "<?php echo htmlspecialchars($search_query); ?>"</p>
                                <p style="font-size: 0.9rem;">Try searching with different keywords</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Friend Requests Section -->
            <div class="section-card">
                <h2>Friend Requests</h2>
                <?php if (mysqli_num_rows($requests_result) > 0): ?>
                    <div class="user-list">
                        <?php while ($requester = mysqli_fetch_assoc($requests_result)): ?>
                            <div class="user-item">
                                <div class="user-info">
                                    <img src="profile/<?php echo $requester['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                                    <div>
                                        <p class="username"><?php echo $requester['username']; ?></p>
                                        <p class="user-name"><?php echo $requester['firstname'] . ' ' . $requester['lastname']; ?></p>
                                    </div>
                                </div>
                                <div class="user-actions">
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="requester_id" value="<?php echo $requester['id']; ?>">
                                        <button type="submit" name="accept_request" class="btn-small btn-success">Accept</button>
                                    </form>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="requester_id" value="<?php echo $requester['id']; ?>">
                                        <button type="submit" name="reject_request" class="btn-small btn-danger">Reject</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-text">No pending friend requests</p>
                <?php endif; ?>
            </div>
            
            <!-- Sent Requests Section -->
            <div class="section-card">
                <h2>📤 Sent Requests</h2>
                <?php if (mysqli_num_rows($sent_requests_result) > 0): ?>
                    <div class="user-list">
                        <?php while ($recipient = mysqli_fetch_assoc($sent_requests_result)): ?>
                            <div class="user-item">
                                <div class="user-info">
                                    <img src="profile/<?php echo $recipient['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                                    <div>
                                        <p class="username"><?php echo $recipient['username']; ?></p>
                                        <p class="user-name"><?php echo $recipient['firstname'] . ' ' . $recipient['lastname']; ?></p>
                                    </div>
                                </div>
                                <div class="user-actions">
                                    <span style="color: #ff9800; font-weight: bold; font-size: 0.9rem;">⏳ Pending</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-text">No sent requests waiting for response</p>
                <?php endif; ?>
            </div>
            
            <!-- Friends Section -->
            <div class="section-card">
                <h2>My Friends (<?php echo mysqli_num_rows($friends_result); ?>)</h2>
                <?php if (mysqli_num_rows($friends_result) > 0): ?>
                    <div class="user-list">
                        <?php while ($friend = mysqli_fetch_assoc($friends_result)): ?>
                            <div class="user-item">
                                <div class="user-info">
                                    <img src="profile/<?php echo $friend['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                                    <div>
                                        <a href="visit_profile.php?id=<?php echo $friend['id']; ?>" class="username"><?php echo $friend['username']; ?></a>
                                        <p class="user-name"><?php echo $friend['firstname'] . ' ' . $friend['lastname']; ?></p>
                                    </div>
                                </div>
                                <div class="user-actions">
                                    <a href="messages.php?user_id=<?php echo $friend['id']; ?>" class="btn-small btn-primary">Message</a>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="friend_id" value="<?php echo $friend['id']; ?>">
                                        <button type="submit" name="unfriend" class="btn-small btn-danger">Unfriend</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-text">No friends yet. Add some!</p>
                <?php endif; ?>
            </div>
            
            <!-- Suggested Users Section -->
            <div class="section-card">
                <h2>Suggested Users</h2>
                <?php if (mysqli_num_rows($suggested_result) > 0): ?>
                    <div class="user-list">
                        <?php while ($suggested = mysqli_fetch_assoc($suggested_result)): ?>
                            <div class="user-item">
                                <div class="user-info">
                                    <img src="profile/<?php echo $suggested['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                                    <div>
                                        <a href="visit_profile.php?id=<?php echo $suggested['id']; ?>" class="username"><?php echo $suggested['username']; ?></a>
                                        <p class="user-name"><?php echo $suggested['firstname'] . ' ' . $suggested['lastname']; ?></p>
                                    </div>
                                </div>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="receiver_id" value="<?php echo $suggested['id']; ?>">
                                    <button type="submit" name="send_request" class="btn-small btn-primary">Add Friend</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-text">No suggestions available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
