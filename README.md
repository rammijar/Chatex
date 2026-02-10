# 🚀 Chatex - Social Media Platform

A simple, beginner-friendly social media platform built for **BCA 6th Semester** project. Connect with friends, share posts, send messages, and enjoy a complete social networking experience.

![Version](https://img.shields.io/badge/version-1.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.0+-green)
![MySQL](https://img.shields.io/badge/MySQL-5.5+-orange)
![License](https://img.shields.io/badge/license-MIT-purple)

---

## 📋 Features

### ✅ User Management
- **User Registration** with validation and profile pictures
- **Admin Approval System** - new users require admin approval before accessing
- **User Profiles** - view and edit personal information
- **Profile Pictures** - upload and display user avatars
- **Online/Offline Status** - track user availability in real-time

### 👥 Friend System
- **Send Friend Requests** - connect with other users
- **Accept/Reject Requests** - manage incoming friend requests
- **Track Sent Requests** - see pending requests you've sent (with ⏳ Pending status)
- **Search Users** - powerful search by username, first name, or last name
- **Smart Search** - auto-excludes already friends and pending requests
- **Unfriend Users** - remove connections anytime
- **Auto-Clear Messages** - conversations deleted when unfriending

### 📝 Posts & Content
- **Create Posts** - share text updates or images
- **Post with Images** - upload images with posts (up to 4MB)
- **Like/Dislike Posts** - interactive engagement (mutually exclusive)
- **Comment on Posts** - discuss with your network
- **Delete Own Posts** - remove your posts anytime
- **Delete Own Comments** - remove your comments from any post
- **Like Counter** - see total likes/dislikes on each post

### 💬 Messaging System
- **Send Messages** - private 1-to-1 messaging with friends
- **Message History** - view past conversations with friends
- **Unread Badges** - see notification count for unread messages
- **Auto-Scroll** - new messages scroll into view automatically
- **Sender Name** - clear identification of who sent each message
- **Navbar Badge** - global unread message indicator
- **Conversation Sidebar** - quick access to all conversations

### 🔧 Admin Panel
- **User Statistics** - total users, pending approvals, accepted users
- **Approve Users** - accept pending registrations
- **Reject Users** - decline registrations and remove applicants
- **Delete Users** - manage user accounts
- **User Management Dashboard** - view all registered users

### 🎨 UI/UX
- **Responsive Design** - works on desktop and mobile devices
- **Clean Interface** - simple and intuitive navigation
- **Notification Badges** - unread message alerts in navbar and sidebar
- **Message Bubbles** - WhatsApp-style messaging interface
- **Professional Styling** - modern colors and layouts
- **Admin Links** - orange-highlighted admin access for admin users
- **Footer** - professional footer with developer credits and links

---

## 🛠️ Prerequisites

Before you begin, ensure you have:

- **XAMPP** (Apache + MySQL + PHP) - [Download](https://www.apachefriends.org/)
- **Web Browser** - Chrome, Firefox, Safari, or Edge
- **Text Editor** - VS Code, Sublime, Notepad++, or similar

### System Requirements
- PHP 7.0 or higher
- MySQL 5.5 or higher
- Apache web server (included in XAMPP)
- 100MB free disk space

---

## 📥 Installation & Setup

### Step 1: Extract Project
```bash
# Extract to XAMPP htdocs folder
# Path: C:\xampp\htdocs\chatex\
```

### Step 2: Start XAMPP Services
1. Open XAMPP Control Panel
2. Click **Start** next to Apache
3. Click **Start** next to MySQL

### Step 3: Create Database
1. Go to `http://localhost/phpmyadmin/`
2. Click **SQL** tab
3. Open `chatex.sql` and copy all content
4. Paste into SQL tab
5. Click **Go** to execute

**OR** Import directly:
- Go to **Import** tab
- Select `chatex.sql` file
- Click **Go**

### Step 4: Access Platform
```
http://localhost/chatex/
```

---

## 🔐 Default Admin Login

```
Username: admin
Password: admin123
```

⚠️ **IMPORTANT**: Change password after first login!

---

## 📂 Project Structure

```
chatex/
├── include/
│   ├── config.php              # Database connection
│   ├── session.php             # Session verification
│   ├── header.php              # Navigation navbar
│   └── footer.php              # Footer component
│
├── css/
│   └── style.css               # Complete styling (900+ lines)
│
├── js/
│   └── script.js               # JavaScript utilities
│
├── profile/                    # User profile pictures
├── posts/                      # Post images
├── messages_img/               # Message images
│
├── index.php                   # Home/Newsfeed
├── login.php                   # User login
├── register.php                # User registration
├── logout.php                  # Logout handler
├── friends.php                 # Friends & search
├── messages.php                # Messaging
├── profile.php                 # User profile
├── visit_profile.php           # View other profiles
├── view_post.php               # Post details
├── admin.php                   # Admin panel
│
├── chatex.sql                  # Database schema
├── README.md                   # This file
└── SETUP.md                    # Setup instructions
```

---

## 🚀 Quick Start

### Register New Account
1. Click **Register**
2. Fill all fields:
   - Username (unique, alphanumeric)
   - First & Last Name
   - Email address
   - Phone number
   - Password (min 8 chars recommend)
   - Profile picture (optional, jpg/png/gif)
3. Click **Register**
4. **Wait for admin approval** before logging in

### Login
1. Go to login page
2. Enter username and password
3. Click **Login**
4. Redirected to Newsfeed if approved

### Explore Features

#### 🏠 **Home/Newsfeed**
- Create posts with text and optional images
- Like (👍) or Dislike (👎) posts
- View and add comments
- Delete your own posts

#### 👥 **Friends**
- **🔍 Search Users** - search by name or username
- **📤 Sent Requests** - see pending requests you sent
- **📥 Friend Requests** - accept/reject incoming requests
- **👫 Friends List** - view and manage friends
- **💡 Suggestions** - recommended users to add
- Auto-remove conversations when unfriending

#### 💬 **Messages**
- Send private messages to friends
- View conversation history
- 🔔 Unread badges show message count
- Auto-scroll to latest messages
- Sender name displayed on every message

#### 👤 **Profile**
- View and edit your information
- Update bio and contact details
- Change profile picture
- View your posts
- See your friends

#### ⚙️ **Admin Panel** (Admin Only)
- Dashboard with user statistics
- Approve/Reject new registrations
- Manage user accounts
- View all users

---

## 💡 Feature Details

### Like & Dislike System
```
Like a post       → 👍 Like button highlights green
Click again       → Dislike removed, shown in unlike state
Dislike a post    → 👎 Dislike button highlights red
Like & Dislike    → Mutually exclusive (can't have both)
```

### Friend Request Flow
```
Send Request → User receives in "Friend Requests" 
           ↓
        Accept → Both added to friends, can message
        Reject → Request deleted
```

### Admin Approval Workflow
```
User Registers → approved_status = 0 (pending)
              ↓
           Admin Reviews → Approves (status = 1) or Rejects
              ↓
           User Approved → Can now login
```

### Search Intelligence
Excludes:
- Currently logged-in user
- Already friended users
- Users with pending friend requests received
- Users with pending friend requests sent

---

## 📊 Database Schema

### 8 Tables Total

| Table | Purpose |
|-------|---------|
| **users** | User accounts and profiles |
| **friend_requests** | Pending and accepted requests |
| **friends** | Established friendships |
| **messages** | Private messages |
| **posts** | Status updates |
| **comments** | Post comments |
| **likes** | Post likes |
| **dislikes** | Post dislikes |

### Key Fields
```sql
users: id, username, password(MD5), firstname, lastname, 
       email, phone, profile_pic, bio, approved_status, online_status

posts: id, user_id, post_text, image, created_date

comments: id, post_id, user_id, comment_text, created_date

messages: id, sender_id, receiver_id, message_text, is_read, sent_date

friend_requests: id, requester_id, receiver_id, status, request_date
```

---

## 🔒 Security Features

- **Password Hashing** - MD5 (suitable for BCA project)
- **Session Management** - PHP sessions for authentication
- **Input Validation** - Regex patterns for all inputs
- **SQL Injection Prevention** - `mysqli_real_escape_string()`
- **File Upload Validation** - Type and size checking
- **XSS Prevention** - `htmlspecialchars()` on output
- **Access Control** - Check user ownership before delete

---

## 🐛 Troubleshooting

### Issue: "Access Denied" or "Login Failed"
**Solution:**
- Verify account is approved by admin
- Check username/password spelling
- Clear browser cache (Ctrl+Shift+Delete)

### Issue: Images Not Displaying
**Solution:**
- Ensure `/profile`, `/posts`, `/messages_img` folders exist
- Check folder permissions (chmod 0777 on Linux)
- Verify file paths in img src attribute

### Issue: "Database Connection Failed"
**Solution:**
- Start MySQL in XAMPP Control Panel
- Verify credentials in `include/config.php`
- Check database query in phpmyadmin

### Issue: Friend Request Not Appearing
**Solution:**
- Refresh page (F5)
- Verify friend account is approved
- Check sent_requests section to confirm it was sent

### Issue: Can't Upload Profile Picture
**Solution:**
- Check image size (max 2MB for profiles, 4MB for posts)
- Verify format (jpg, jpeg, png, gif only)
- Ensure profile folder is writable

---

## 🎓 Learning Concepts

This project teaches:
- ✅ User authentication & authorization
- ✅ Database design & relationships
- ✅ Form validation & error handling
- ✅ File upload processing
- ✅ Session management
- ✅ CRUD operations via web
- ✅ Responsive web design
- ✅ MVC architecture basics

---
## 👨‍💻 Contributors

<p>
  <img src="https://github.com/rammijar.png" width="38" style="border-radius:50%; vertical-align:middle;" />
  <a href="https://github.com/rammijar"><strong>Ram Mijar</strong></a>
  <a href="https://www.rammijar.com.np/">
    <img src="https://img.shields.io/badge/Website-000000?style=flat-square" />
  </a>
</p>

<p>
  <img src="https://github.com/rajkc2024.png" width="38" style="border-radius:50%; vertical-align:middle;" />
  <a href="https://github.com/rajkc2024"><strong>Raj K.C</strong></a>
  <a href="https://www.kcraj.com.np/">
    <img src="https://img.shields.io/badge/Website-000000?style=flat-square" />
  </a>
</p>

---

<p align="center">
  Made with ❤️
</p>
---

## 📝 License

MIT License - Free for educational and personal use

---

## ✨ Future Enhancements

- Real-time WebSocket messaging
- Email notifications
- Two-factor authentication
- Story feature (24-hour posts)
- Post sharing
- User blocking
- Report & moderation system
- Dark mode theme
- Mobile app version

---

## 📞 Support

1. Check **Troubleshooting** section above
2. Review code comments (well-documented)
3. Check browser console (F12 → Console tab)
4. Verify database tables in phpMyAdmin
5. Visit GitHub for additional resources

---

**Version:** 1.0  
**Last Updated:** 2026-02-07  
**Status:** ✅ Complete & Functional

### Core Files

#### `include/config.php`
- Establishes database connection
- Uses MySQLi extension
- Database: chatex_db
- Credentials: root (no password)

#### `include/session.php`
- Manages user sessions
- Verifies if user is logged in
- Redirects to login if not authenticated

#### `include/header.php`
- Common navigation bar
- Conditional menu based on login status
- Logo and navigation links

### Authentication Files

#### `register.php`
- User registration form
- Input validation (username, email, phone, password)
- Profile picture upload
- Password hashing with MD5
- Duplicate check for username, email, phone

#### `login.php`
- User login form
- Credential verification
- Session creation
- Online status update

#### `logout.php`
- Destroys user session
- Updates offline status
- Redirects to login page

### Main Features

#### `index.php` (Home/Newsfeed)
- Display all posts from all users
- Create new posts
- Like/Unlike posts
- View comments
- Quick links sidebar

#### `friends.php` (Friend Management)
- View pending friend requests
- Accept/Reject requests
- View all friends
- Suggested users to add
- Unfriend functionality

#### `messages.php` (Messaging)
- Conversation with friends
- Send messages
- View message history
- Friends list sidebar
- Online/Offline status

#### `profile.php` (User Profile)
- View own profile
- Edit profile information
- Upload profile picture
- View own posts
- Bio management

#### `visit_profile.php` (Other User Profiles)
- View other user profiles
- Send friend requests
- View user's posts
- Send messages if friends
- Online status indicator

#### `view_post.php` (Post Details)
- View full post
- Add comments
- Like/Unlike post
- View all comments
- Comment author details

---

## DATABASE SCHEMA

### Users Table
```sql
id, username, password, firstname, lastname, email, phone, 
profile_pic, bio, registration_date, approved_status, online_status
```

### Friend Requests Table
```sql
id, requester_id, receiver_id, status, request_date
Status: pending, accepted, rejected
```

### Friends Table
```sql
id, user_id_1, user_id_2, friend_date
```

### Messages Table
```sql
id, sender_id, receiver_id, message_text, image, 
sent_date, is_read
```

### Posts Table
```sql
id, user_id, post_text, image, created_date
```

### Comments Table
```sql
id, post_id, user_id, comment_text, created_date
```

### Likes Table
```sql
id, post_id, user_id, created_date
```

---

## INSTALLATION GUIDE

### Requirements
- XAMPP (or Apache + MySQL + PHP)
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Modern web browser

### Windows Installation

1. **Install XAMPP**
   - Download from https://www.apachefriends.org/
   - Install in default location

2. **Copy Project**
   - Copy the `chatex` folder to `C:\xampp\htdocs\`

3. **Create Database**
   - Open http://localhost/phpmyadmin
   - Create new database: `chatex_db`
   - Import `chatex.sql` file
   - Click on `chatex_db` → Import → Select chatex.sql

4. **Run Project**
   - Start Apache and MySQL in XAMPP Control Panel
   - Open browser: http://localhost/chatex
   - You will be redirected to login page

### Linux Installation

```bash
# Install Apache, MySQL, PHP
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-curl php-gd

# Copy project
sudo cp -r chatex /var/www/html/

# Set permissions
sudo chmod -R 777 /var/www/html/chatex
sudo chown -R www-data:www-data /var/www/html/chatex

# Create database
mysql -u root -p
CREATE DATABASE chatex_db;
USE chatex_db;
SOURCE /var/www/html/chatex/chatex.sql;

# Start services
sudo systemctl start apache2
sudo systemctl start mysql

# Access project
# Open browser: http://localhost/chatex
```

---

## USAGE

### Registration
1. Click "Register" on login page
2. Fill in all required fields
3. Upload profile picture (optional)
4. Click "Register"
5. Login with credentials

### Login
1. Enter username and password
2. Click "Login"
3. Redirected to home page

### Creating Posts
1. Go to Home (Index)
2. Type in text area "What's on your mind?"
3. Click "Post"
4. Post appears on newsfeed

### Making Friends
1. Go to Friends page
2. View suggested users
3. Click "Add Friend"
4. Receiver gets notification of pending request
5. Receiver can Accept/Reject request
6. Once accepted, users become friends

### Messaging
1. Go to Messages page
2. Select friend from sidebar
3. Type message in input box
4. Click "Send"
5. Messages are displayed in conversation

### Commenting on Posts
1. Click on post
2. Scroll to comments section
3. Type comment
4. Click "Post Comment"
5. Comment appears below post

---

## TECHNICAL DETAILS

### Security Features
- **Password Hashing**: MD5 hashing algorithm
- **Session Management**: PHP sessions for user authentication
- **Input Validation**: Regex patterns for email, phone, username
- **SQL Injection Prevention**: MySQLi prepared statements

### Technologies Used
- **Frontend**: HTML5, CSS3, JavaScript (basic)
- **Backend**: PHP 7.0+
- **Database**: MySQL 5.7+
- **Server**: Apache Web Server

### Libraries/Extensions Used
- MySQLi Extension (Database)
- PHP File Upload Handling
- PHP Session Management

---

## COMMON ERRORS & SOLUTIONS

### Error 1: Connection Failed
```
Problem: "Connection Failed: Unable to connect to server"
Solution: 
1. Check if MySQL is running
2. Verify config.php has correct credentials
3. Ensure database 'chatex_db' exists
```

### Error 2: Session Already Started
```
Problem: "session_start(): Ignoring session_start()"
Solution:
1. Check that session.php is included before other includes
2. Ensure no whitespace before <?php tag
```

### Error 3: File Upload Failed
```
Problem: "Failed to upload profile picture"
Solution:
1. Verify profile/ folder has write permissions (chmod 777)
2. Check file size is less than 2MB
3. Ensure file format is jpg/jpeg/png/gif
```

### Error 4: 404 Not Found
```
Problem: "Error 404 - File not found"
Solution:
1. Verify project is in C:\xampp\htdocs\chatex\
2. Check Apache is running
3. Verify correct URL: http://localhost/chatex/
```

---

## FEATURES EXPLAINED

### Post Liking Mechanism
- Users can like posts without duplicate likes
- Like counter updates in real-time
- Unlike removes the like from database

### Friend Request System
1. User sends request → Status: pending
2. Receiver receives notification in "Friend Requests" section
3. Receiver accepts → Status: accepted, added to friends
4. Users can now message each other

### Message System
- Messages are stored in database
- Each message tracks sender, receiver, timestamp
- Mark as read functionality
- Messages displayed in conversation style

### Comments on Posts
- Users can comment on any post
- Comments display with author info
- Comments ordered by date (oldest first)

---

## FUTURE ENHANCEMENTS

1. **Image Sharing in Posts/Messages**
   - Upload images with posts
   - Image galleries

2. **Real-time Notifications**
   - Notifications for friend requests
   - Message notifications
   - Post comments notifications

3. **Search Functionality**
   - Search users by username/name
   - Search posts by content

4. **User Blocking**
   - Block/Unblock users
   - Privacy settings

5. **Story Feature**
   - Stories that disappear after 24 hours
   - Story views tracking

6. **Advanced Security**
   - RSA Encryption for messages
   - Two-factor authentication
   - Password strength requirements

7. **UI/UX Improvements**
   - Infinite scroll for posts
   - Dark mode
   - Mobile app version

---

## TESTING CHECKLIST

### Registration
- ✓ Valid registration with all fields
- ✓ Duplicate username error
- ✓ Invalid email format error
- ✓ Password length validation
- ✓ Profile picture upload

### Login
- ✓ Correct credentials login
- ✓ Invalid credentials error
- ✓ Session creation
- ✓ Login redirect to home

### Posts
- ✓ Create post
- ✓ Display all posts on home
- ✓ Like/Unlike posts
- ✓ Comment on posts

### Friends
- ✓ Send friend request
- ✓ Accept/Reject request
- ✓ View friends list
- ✓ Unfriend user

### Messages
- ✓ Send message to friend
- ✓ View message history
- ✓ Mark messages as read
- ✓ Multiple conversations

---

## CONCLUSION

Chatex is a beginner-friendly social media platform suitable for BCA semester projects. It demonstrates fundamental concepts of web development including:
- Database design and management
- User authentication
- CRUD operations
- Session management
- Form validation
- File handling

The project is intentionally kept simple to be understandable for beginners while covering essential social media features.

---

## CONTACT & SUPPORT

For issues or questions:
1. Check error messages carefully
2. Review file permissions
3. Verify database connection
4. Check browser console for JavaScript errors
5. Re-import database schema

---

**Project Created**: 2026
**Version**: 1.0
**Status**: COMPLETE

