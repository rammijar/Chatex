<?php
// Chatex Social Media Platform
// Messages Page
// Allows users to send and receive messages with friends

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
$selected_chat_user = null;
$selected_chat_user_id = null;

// Determine which user to chat with
if (isset($_GET['user_id'])) {
    $selected_chat_user_id = $_GET['user_id'];
    
    // Get selected user details
    $selected_query = "SELECT * FROM users WHERE id = '$selected_chat_user_id'";
    $selected_result = mysqli_query($conn, $selected_query);
    $selected_chat_user = mysqli_fetch_assoc($selected_result);
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message_text = trim($_POST['message_text']);
    
    if (!empty($message_text)) {
        // Insert message
        $insert_query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES ('$user_id', '$receiver_id', '$message_text')";
        
        if (mysqli_query($conn, $insert_query)) {
            $message = "Message sent!";
        }
    }
}

// Get list of friends for sidebar with unread message count
$friends_query = "SELECT u.*, 
                  (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = '$user_id' AND is_read = 0) as unread_count,
                  (SELECT MAX(sent_date) FROM messages WHERE (sender_id = u.id AND receiver_id = '$user_id') OR (sender_id = '$user_id' AND receiver_id = u.id)) as last_message_time
                  FROM users u
                  JOIN friends f ON (f.user_id_1 = u.id OR f.user_id_2 = u.id)
                  WHERE (f.user_id_1 = '$user_id' OR f.user_id_2 = '$user_id') AND u.id != '$user_id'
                  ORDER BY last_message_time DESC, u.username";
$friends_result = mysqli_query($conn, $friends_query);

// Get messages for selected user
$chat_messages = null;
if ($selected_chat_user_id) {
    $messages_query = "SELECT * FROM messages 
                       WHERE (sender_id = '$user_id' AND receiver_id = '$selected_chat_user_id')
                       OR (sender_id = '$selected_chat_user_id' AND receiver_id = '$user_id')
                       ORDER BY sent_date ASC";
    $chat_messages = mysqli_query($conn, $messages_query);
    
    // Mark messages as read
    $mark_read = "UPDATE messages SET is_read = 1 WHERE sender_id = '$selected_chat_user_id' AND receiver_id = '$user_id'";
    mysqli_query($conn, $mark_read);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .messages-container {
            display: grid !important;
            grid-template-columns: 280px 1fr;
            gap: 0;
            height: calc(100vh - 150px);
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .messages-sidebar {
            background: white;
            border-right: 1px solid #ddd;
            padding: 0;
            overflow-y: auto;
            box-shadow: none !important;
        }
        
        .messages-sidebar h3 {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            margin: 0;
            border-bottom: 1px solid #ddd;
        }
        
        .friends-list {
            padding: 0;
            margin: 0;
        }
        
        .friend-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0 !important;
            position: relative;
        }
        
        .friend-item.has-unread {
            background-color: #e8f5e9;
            font-weight: bold;
        }
        
        .unread-badge {
            position: absolute;
            right: 1.5rem;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .messages-main {
            display: flex !important;
            flex-direction: column;
            background: white;
            height: 100%;
        }
        
        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            gap: 1rem;
            background: #f9f9f9;
        }
        
        .messages-display {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: #fafafa;
        }
        
        .message-bubble {
            padding: 0.75rem 1.25rem;
            border-radius: 18px;
            max-width: 70%;
            word-wrap: break-word;
            font-size: 1rem;
        }
        
        .message-bubble.sent {
            background-color: #4CAF50;
            color: white;
            align-self: flex-end;
        }
        
        .message-bubble.received {
            background-color: #e0e0e0;
            color: #333;
            align-self: flex-start;
        }
        
        .message-bubble small {
            display: block;
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.5rem;
        }
        
        .message-input-section {
            padding: 1.5rem;
            border-top: 1px solid #ddd;
            background: white;
        }
        
        .message-form {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }
        
        .message-input {
            flex: 1;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 24px;
            resize: none;
            min-height: 45px;
            max-height: 120px;
            font-family: inherit;
            font-size: 1rem;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
        }
        
        .message-form button {
            padding: 0.75rem 1.5rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 24px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .message-form button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
        
        .empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #999;
            flex-direction: column;
        }
        
        @media (max-width: 768px) {
            .messages-container {
                grid-template-columns: 100px 1fr;
            }
            
            .messages-sidebar h3 {
                font-size: 0.9rem;
            }
            
            .friend-item {
                flex-direction: column;
                padding: 0.75rem;
            }
            
            .message-bubble {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="messages-container">
        <!-- Friends list sidebar -->
        <div class="messages-sidebar">
            <h3>💬 Chats</h3>
            <div class="friends-list">
                <?php if (mysqli_num_rows($friends_result) > 0): ?>
                    <?php while ($friend = mysqli_fetch_assoc($friends_result)): ?>
                        <a href="messages.php?user_id=<?php echo $friend['id']; ?>" 
                           class="friend-item <?php echo ($selected_chat_user_id == $friend['id']) ? 'active' : ''; ?> <?php echo ($friend['unread_count'] > 0) ? 'has-unread' : ''; ?>">
                            <img src="profile/<?php echo $friend['profile_pic']; ?>" alt="Profile" class="profile-pic-tiny">
                            <div style="flex: 1;">
                                <div><?php echo $friend['username']; ?></div>
                                <small style="color: #999; font-size: 0.8rem;">
                                    <?php echo $friend['online_status'] ? '🟢 Online' : '🔴 Offline'; ?>
                                </small>
                            </div>
                            <?php if ($friend['unread_count'] > 0): ?>
                                <span class="unread-badge"><?php echo $friend['unread_count']; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-text" style="padding: 1.5rem;">No friends yet</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Chat area -->
        <div class="messages-main">
            <?php if ($selected_chat_user): ?>
                <!-- Chat header -->
                <div class="chat-header">
                    <img src="profile/<?php echo $selected_chat_user['profile_pic']; ?>" alt="Profile" class="profile-pic-small">
                    <div>
                        <h3 style="margin: 0;"><?php echo $selected_chat_user['username']; ?></h3>
                        <p class="chat-status" style="margin: 0; font-size: 0.85rem;">
                            <?php echo $selected_chat_user['online_status'] ? '🟢 Online' : '🔴 Offline'; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Messages display -->
                <div class="messages-display">
                    <?php if ($chat_messages && mysqli_num_rows($chat_messages) > 0): ?>
                        <?php while ($msg = mysqli_fetch_assoc($chat_messages)): ?>
                            <div class="message-bubble <?php echo ($msg['sender_id'] == $user_id) ? 'sent' : 'received'; ?>">
                                <?php if ($msg['sender_id'] != $user_id): ?>
                                    <small style="display: block; margin-bottom: 0.25rem; opacity: 0.9;">
                                        <strong><?php echo $selected_chat_user['username']; ?></strong>
                                    </small>
                                <?php endif; ?>
                                <p style="margin: 0;"><?php echo htmlspecialchars($msg['message_text']); ?></p>
                                <small><?php echo date('g:i A', strtotime($msg['sent_date'])); ?></small>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>👋 Start the conversation!</p>
                            <small>No messages yet</small>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Message input -->
                <div class="message-input-section">
                    <form method="POST" class="message-form">
                        <input type="hidden" name="receiver_id" value="<?php echo $selected_chat_user_id; ?>">
                        <textarea name="message_text" placeholder="Type a message..." class="message-input" required></textarea>
                        <button type="submit" name="send_message">Send</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Select a friend to start messaging 💬</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            const messagesDisplay = document.querySelector('.messages-display');
            if (messagesDisplay) {
                messagesDisplay.scrollTop = messagesDisplay.scrollHeight;
            }
            
            // Auto-resize textarea
            const textarea = document.querySelector('.message-input');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });
            }
        });
    </script>
    <?php include('include/footer.php'); ?>
</body>
</html>
