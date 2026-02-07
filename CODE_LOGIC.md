# CHATEX PROJECT - CODE LOGIC GUIDE

## Overview
This document explains the logic and flow of the main PHP files in Chatex.

---

## AUTHENTICATION FLOW

### register.php - Registration Process

```
USER SUBMITS REGISTRATION FORM
    ↓
VALIDATE INPUT
    ├─ Check username format (alphanumeric, 3-20 chars)
    ├─ Check password length (min 6 chars)
    ├─ Check passwords match
    ├─ Check email format
    ├─ Check phone format (10 digits)
    └─ Check for duplicates in database
    ↓
IF VALIDATION FAILS → DISPLAY ERRORS
    ↓
IF UPLOAD PROFILE PIC → SAVE TO FOLDER
    ↓
HASH PASSWORD WITH MD5
    ↓
INSERT USER INTO DATABASE
    ↓
REDIRECT TO LOGIN PAGE
```

### login.php - Login Process

```
USER SUBMITS LOGIN FORM
    ↓
GET USERNAME & PASSWORD
    ↓
HASH PASSWORD WITH MD5
    ↓
QUERY DATABASE:
    SELECT * FROM users 
    WHERE username='$username' AND password='$hashed_pass'
    ↓
IF USER FOUND
    ├─ CREATE SESSION VARIABLES
    ├─ UPDATE online_status = 1
    └─ REDIRECT TO index.php
    ↓
IF USER NOT FOUND
    └─ DISPLAY ERROR MESSAGE
```

### session.php - Session Check

```
ON EVERY PAGE LOAD
    ↓
CHECK FUNCTION session_status()
    If session not started → START SESSION
    ↓
CHECK IF $_SESSION['user_id'] EXISTS
    ├─ If YES → Continue with page
    └─ If NO → REDIRECT TO login.php
```

---

## POST MANAGEMENT LOGIC

### index.php - Home/Newsfeed

```
USER LOGS IN → GOES TO index.php
    ↓
QUERY ALL POSTS:
    SELECT posts.* FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY created_date DESC
    ↓
FOR EACH POST DISPLAY:
    ├─ User profile picture
    ├─ Username
    ├─ Post date
    ├─ Post content
    ├─ Like count
    └─ Comment count

USER CREATES NEW POST:
    ├─ GET post_text FROM form
    ├─ VALIDATE post_text (not empty)
    ├─ INSERT INTO posts TABLE
    │   INSERT INTO posts (user_id, post_text) 
    │   VALUES ($user_id, $post_text)
    └─ REFRESH PAGE

USER CLICKS LIKE:
    ├─ CHECK IF ALREADY LIKED
    │   SELECT * FROM likes 
    │   WHERE post_id = $post_id AND user_id = $user_id
    ├─ IF YES → DELETE LIKE (Unlike)
    │   DELETE FROM likes WHERE ...
    └─ IF NO → INSERT LIKE
        INSERT INTO likes (post_id, user_id) VALUES (...)
```

### view_post.php - Post Details & Comments

```
USER VISITS POST (view_post.php?id=123)
    ↓
GET POST ID FROM URL
    ↓
QUERY POST DETAILS:
    SELECT posts.*, user.username, user.profile_pic
    FROM posts JOIN users
    WHERE posts.id = $post_id
    ↓
QUERY ALL COMMENTS:
    SELECT * FROM comments
    WHERE post_id = $post_id
    ORDER BY created_date ASC
    ↓
DISPLAY POST & COMMENTS
    ↓
USER ADDS COMMENT:
    ├─ GET comment_text FROM form
    ├─ VALIDATE (not empty)
    ├─ INSERT INTO comments:
    │   INSERT INTO comments (post_id, user_id, comment_text)
    │   VALUES ($post_id, $user_id, $comment_text)
    └─ RE-FETCH & DISPLAY COMMENTS
```

---

## FRIEND SYSTEM LOGIC

### friends.php - Friend Management

```
USER GOES TO friends.php
    ↓
LOAD THREE SECTIONS:
│
├─ SECTION 1: FRIEND REQUESTS (Incoming)
│   ├─ QUERY pending requests:
│   │   SELECT * FROM friend_requests
│   │   WHERE receiver_id = $user_id AND status = 'pending'
│   │
│   ├─ DISPLAY BUTTONS: ACCEPT or REJECT
│   │
│   └─ USER CLICKS ACCEPT:
│       ├─ UPDATE friend_requests SET status = 'accepted'
│       └─ INSERT INTO friends (user_id_1, user_id_2)
│
├─ SECTION 2: FRIENDS LIST
│   ├─ QUERY friends:
│   │   SELECT * FROM friends
│   │   WHERE user_id_1 = $user_id OR user_id_2 = $user_id
│   │
│   ├─ FOR EACH FRIEND SHOW:
│   │   ├─ Profile picture
│   │   ├─ Username
│   │   ├─ MESSAGE button
│   │   └─ UNFRIEND button
│   │
│   └─ IF UNFRIEND CLICKED:
│       DELETE FROM friends 
│       WHERE (user_id_1 = $user_id AND user_id_2 = $friend_id)
│       OR (user_id_1 = $friend_id AND user_id_2 = $user_id)
│
└─ SECTION 3: SUGGESTED USERS
    ├─ QUERY users NOT in friends and NOT pending:
    │   SELECT * FROM users u
    │   WHERE u.id NOT IN (SELECT user_id_2 FROM friends WHERE user_id_1 = $user_id)
    │   AND u.id NOT IN (SELECT requester_id FROM friend_requests WHERE receiver_id = $user_id)
    │
    └─ USER CLICKS ADD FRIEND:
        INSERT INTO friend_requests 
        (requester_id, receiver_id, status)
        VALUES ($user_id, $suggested_user_id, 'pending')
```

---

## MESSAGING LOGIC

### messages.php - Send & Receive Messages

```
USER GOES TO messages.php
    ↓
LEFT SIDEBAR: SHOW ALL FRIENDS
    ├─ QUERY friends:
    │   SELECT users.* FROM users
    │   JOIN friends ON friends.user_id_1 = users.id
    │   WHERE friends.user_id_2 = $user_id
    │
    └─ EACH FRIEND = CLICKABLE LINK
        HREF: messages.php?user_id=friend_id
        ↓
MAIN AREA: SELECTED CONVERSATION
    ├─ IF user_id in URL:
    │   ├─ SHOW selected friend header
    │   │
    │   ├─ QUERY MESSAGES:
    │   │   SELECT * FROM messages
    │   │   WHERE (sender_id = $user_id AND receiver_id = $selected_user)
    │   │   OR (sender_id = $selected_user AND receiver_id = $user_id)
    │   │   ORDER BY sent_date ASC
    │   │
    │   ├─ DISPLAY MESSAGES:
    │   │   ├─ LEFT align if RECEIVED
    │   │   ├─ RIGHT align if SENT
    │   │   └─ Show timestamp
    │   │
    │   ├─ MARK AS READ:
    │   │   UPDATE messages SET is_read = 1
    │   │   WHERE sender_id = $selected_user AND receiver_id = $user_id
    │   │
    │   └─ FORM TO SEND MESSAGE:
    │       ├─ TEXTAREA for message
    │       ├─ USER CLICKS SEND
    │       ├─ INSERT INTO messages:
    │       │   INSERT INTO messages 
    │       │   (sender_id, receiver_id, message_text, sent_date)
    │       │   VALUES ($user_id, $selected_user, $message_text, NOW())
    │       └─ REFRESH CONVERSATION
    │
    └─ IF NO user_id:
        DISPLAY: "Select a friend to start messaging"
```

---

## USER PROFILE LOGIC

### profile.php - Edit Own Profile

```
USER GOES TO profile.php (own profile)
    ↓
QUERY USER DATA:
    SELECT * FROM users WHERE id = $user_id
    ↓
DISPLAY PROFILE WITH EDIT FORM:
    ├─ Show current profile picture
    ├─ Show current firstname, lastname, email, phone, bio
    └─ Allow editing
        ↓
USER UPDATES FORM:
    ├─ GET new values from form
    ├─ VALIDATE email format
    ├─ IF new profile picture uploading:
    │   └─ SAVE to profile/ folder
    │
    ├─ UPDATE DATABASE:
    │   UPDATE users SET
    │   firstname = $firstname,
    │   lastname = $lastname,
    │   email = $email,
    │   phone = $phone,
    │   bio = $bio,
    │   profile_pic = $new_pic
    │   WHERE id = $user_id
    │
    └─ DISPLAY SUCCESS MESSAGE
        ↓
DISPLAY USER'S OWN POSTS:
    ├─ QUERY posts:
    │   SELECT * FROM posts WHERE user_id = $user_id
    │   ORDER BY created_date DESC
    │
    └─ FOR EACH POST:
        ├─ Show post content
        ├─ Like count
        └─ Comment count
```

### visit_profile.php - View Other User Profile

```
USER CLICKS ON USERNAME/PROFILE LINK
    ↓
visit_profile.php?id=TARGET_USER_ID
    ↓
QUERY TARGET USER DATA:
    SELECT * FROM users WHERE id = $target_user_id
    ↓
CHECK FRIENDSHIP STATUS:
    ├─ QUERY friends:
    │   SELECT * FROM friends
    │   WHERE (user_id_1 = $user_id AND user_id_2 = $target_user_id)
    │   OR (user_id_1 = $target_user_id AND user_id_2 = $user_id)
    │
    ├─ IF FRIENDS:
    │   ├─ SHOW "Send Message" button
    │   └─ LINK TO messages.php?user_id=$target_user_id
    │
    ├─ IF PENDING REQUEST:
    │   └─ SHOW "Request Sent" (disabled button)
    │
    └─ IF NOT FRIENDS:
        └─ SHOW "Add Friend" button
            ├─ USER CLICKS
            ├─ INSERT INTO friend_requests:
            │   (requester_id, receiver_id, status)
            └─ UPDATE BUTTON TO "Request Sent"
            ↓
DISPLAY TARGET USER'S POSTS:
    ├─ QUERY posts:
    │   SELECT * FROM posts WHERE user_id = $target_user_id
    │
    └─ FOR EACH POST:
        ├─ Show content
        ├─ Show like count
        ├─ Show comment count
        └─ LINK: "View Post"
```

---

## DATABASE OPERATIONS SUMMARY

### Key SQL Patterns Used

#### Create (C in CRUD)
```php
// Insert new user
INSERT INTO users (username, password, email) 
VALUES ('john', 'hashed_pass', 'john@email.com')

// Insert post
INSERT INTO posts (user_id, post_text) 
VALUES ($user_id, 'Hello World')

// Insert like
INSERT INTO likes (post_id, user_id) 
VALUES ($post_id, $user_id)
```

#### Read (R in CRUD)
```php
// Get user data
SELECT * FROM users WHERE id = $user_id

// Get all posts
SELECT * FROM posts ORDER BY created_date DESC

// Get friends
SELECT * FROM friends WHERE user_id_1 = $user_id
```

#### Update (U in CRUD)
```php
// Update user profile
UPDATE users SET firstname = 'New Name' WHERE id = $user_id

// Update friend request status
UPDATE friend_requests SET status = 'accepted' WHERE id = $request_id

// Update online status
UPDATE users SET online_status = 1 WHERE id = $user_id
```

#### Delete (D in CRUD)
```php
// Delete like
DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id

// Delete friend
DELETE FROM friends WHERE user_id_1 = $user_id AND user_id_2 = $friend_id

// Reject friend request
DELETE FROM friend_requests WHERE requester_id = $requester_id
```

---

## INPUT VALIDATION PATTERNS

### Regex Patterns Used

```php
// Username: alphanumeric and underscore, 3-20 chars
preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)

// Email format
filter_var($email, FILTER_VALIDATE_EMAIL)

// Phone number: 10 digits
preg_match('/^[0-9]{10}$/', $phone)

// Password: at least 6 characters
strlen($password) >= 6
```

---

## SECURITY MEASURES IMPLEMENTED

1. **Password Hashing**
   - Uses MD5: md5($password)
   - Note: For production, use bcrypt

2. **Session Management**
   - Checks `$_SESSION['user_id']` on protected pages
   - Destroys session on logout

3. **Input Validation**
   - Regex patterns for username, email, phone
   - File type and size validation for uploads

4. **Duplicate Prevention**
   - Checks username, email, phone before inserting
   - Checks like status before inserting like

---

## ERROR HANDLING

### Try-Catch Pattern (adapted for MySQL)

```php
// Execute query
if (mysqli_query($conn, $query)) {
    // Success - continue
    $message = "Operation successful";
} else {
    // Failure - show error
    $message = "Operation failed: " . mysqli_error($conn);
}
```

### Validation Pattern

```php
$errors = array();

if (empty($username)) {
    $errors[] = "Username is required";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (count($errors) > 0) {
    // Display errors
} else {
    // Proceed with operation
}
```

---

## COMMON FUNCTIONS EXPLAINED

### Session Initialization (include/session.php)
```php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Check status to avoid "session already started" error
```

### Database Query Execution
```php
$result = mysqli_query($conn, $query);
// Returns result object or FALSE

mysqli_num_rows($result);
// Returns number of rows in result

mysqli_fetch_assoc($result);
// Returns one row as associative array
```

### Redirect
```php
header("Location: index.php");
exit();
// exit() ensures no further code runs
```

---

## APPLICATION FLOW DIAGRAM

```
START
  ↓
NOT LOGGED IN:
  ├─ show login.php
  ├─ or show register.php
  └─ on success → redirect to index.php
  ↓
LOGGED IN:
  ├─ index.php (home/newsfeed)
  ├─ friends.php (friend management)
  ├─ messages.php (messaging)
  ├─ profile.php (own profile)
  ├─ visit_profile.php (other profiles)
  ├─ view_post.php (post details)
  └─ logout.php → back to login
```

---

## TESTING CHECKLIST

### Unit Testing
- [ ] Register with valid data
- [ ] Register with duplicate username
- [ ] Login with correct password
- [ ] Login with wrong password
- [ ] Create post
- [ ] Like/unlike post
- [ ] Comment on post

### Integration Testing
- [ ] Complete user flow: Register → Login → Post → Like
- [ ] Friend flow: Send request → Accept → Message
- [ ] Multiple users interacting

### Edge Cases
- [ ] Empty inputs
- [ ] SQL injection attempts
- [ ] File upload attacks
- [ ] Session timeout
- [ ] Concurrent logins

---

**Guide Version**: 1.0
**Last Updated**: February 2026

