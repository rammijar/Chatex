# CHATEX INSTALLATION & SETUP GUIDE

## Quick Start Guide for Windows

### Step 1: Prepare the Files
1. The Chatex project is located in: `C:\xampp\htdocs\chatex\`
2. Ensure all files are in place (check directory structure below)

### Step 2: Import Database
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Open browser and go to: `http://localhost/phpmyadmin`
4. Create a new database:
   - Click "New Database"
   - Database name: `chatex_db`
   - Collation: `utf8_general_ci`
   - Click "Create"
5. Import the SQL file:
   - Enter `chatex_db` database
   - Click "Import" tab
   - Choose `chatex.sql` file from project folder
   - Click "Import"

### Step 3: Verify Configuration
1. Open `include/config.php`
2. Verify these settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'chatex_db');
   ```

### Step 4: Set Folder Permissions
The following folders need write permissions:

1. Right-click on `profile` folder → Properties
2. Security tab → Edit → Give "Full Control" to all users
3. Repeat for `messages_img` folder

### Step 5: Start the Application
1. Open browser: `http://localhost/chatex/`
2. You should see the Login page

### Step 6: Create First User (Register)
1. Click "Register here" link
2. Fill in all details:
   - Username: testuser
   - First Name: Test
   - Last Name: User
   - Email: test@example.com
   - Phone: 9876543210
   - Password: password123
   - Confirm Password: password123
3. Click "Register"

### Step 7: Login
1. Use credentials from registration
2. Click "Login"
3. Welcome to Chatex!

---

## Project File Structure

```
C:\xampp\htdocs\chatex\
│
├── include/
│   ├── config.php           # 📄 Database configuration
│   ├── session.php          # 📄 Session management
│   └── header.php           # 📄 Navigation bar
│
├── css/
│   └── style.css            # 🎨 All styling (simple & clean)
│
├── js/
│   └── script.js            # ⚡ JavaScript functions
│
├── profile/                 # 📁 User profile pictures folder
├── messages_img/            # 📁 Message images folder
│
├── index.php                # 🏠 Home/Newsfeed page
├── register.php             # 📝 User registration
├── login.php                # 🔐 User login
├── logout.php               # 🚪 User logout
├── friends.php              # 👥 Friend management
├── messages.php             # 💬 Messaging system
├── profile.php              # 👤 User profile
├── visit_profile.php        # 👁️ View other profiles
├── view_post.php            # 📰 View post details
│
├── chatex.sql               # 💾 Database dump
└── README.md                # 📖 Complete documentation
```

---

## Features Overview

### 1. Authentication System
- **Register**: Create new account with profile picture
- **Login**: Secure login with username/password
- **Logout**: Safely exit the application
- **Password Hashing**: MD5 encryption for security

### 2. User Profile
- View and edit profile information
- Upload profile picture
- Add bio/about me
- View own posts

### 3. Friend System
- Send friend requests
- Accept/reject requests
- View all friends
- Suggest new friends
- Unfriend users

### 4. Messaging
- Real-time messaging with friends
- View message history
- Online/offline status
- Conversation view

### 5. Posts (Newsfeed)
- Create text posts
- View all posts
- Like/unlike posts
- Comment on posts
- View comments

---

## Testing the Application

### Test Account 1
```
Username: testuser
Password: password123
```

### Test Features
1. **Register & Login**
   - Create new account
   - Login with credentials

2. **Create Posts**
   - Go to Home
   - Type "What's on your mind?"
   - Click Post

3. **Make Friends**
   - Go to Friends
   - Click "Add Friend" on suggested users
   - Accept/Reject requests

4. **Send Messages**
   - Go to Messages
   - Select friend
   - Type and send message

5. **Comment & Like**
   - Go to Home
   - Click on post
   - Add comments
   - Like posts

---

## Troubleshooting

### Problem: Database Connection Error
```
Solution:
1. Check MySQL is running in XAMPP
2. Verify database name is 'chatex_db'
3. Verify config.php credentials
4. Check user 'root' exists in MySQL
```

### Problem: Login Redirects to Register
```
Solution:
1. Verify user was created in database
2. Check approved_status = 1 in users table
3. Verify password was hashed correctly
```

### Problem: Can't Upload Profile Picture
```
Solution:
1. Right-click 'profile' folder
2. Select Properties → Security → Edit
3. Give Full Control permissions
4. Check file size < 2MB
```

### Problem: Page Not Found (404)
```
Solution:
1. Verify XAMPP Apache is running
2. Check correct URL: http://localhost/chatex/
3. Verify files are in C:\xampp\htdocs\chatex\
4. Check PHP is enabled in Apache
```

### Problem: Session Issues
```
Solution:
1. Clear browser cookies
2. Restart Apache service
3. Check session.php is included first
4. Check for any output before PHP tags
```

---

## Database Schema Summary

### Users Table
- Stores user account information
- Fields: id, username, password, email, phone, profile_pic, bio, etc.

### Friends Table
- Stores accepted friend relationships
- Links users who are friends with each other

### Friend Requests Table
- Stores pending friend requests
- Status: pending, accepted, rejected

### Messages Table
- Stores all messages between users
- Includes sender, receiver, message text, timestamp

### Posts Table
- Stores all posts from users
- Includes user_id, post_text, created_date

### Comments Table
- Stores comments on posts
- Links comments to posts and users

### Likes Table
- Stores which users liked which posts
- Prevents duplicate likes

---

## Security Notes

1. **Passwords**: Hashed using MD5 (Note: Use bcrypt for production)
2. **Sessions**: Using PHP sessions for authentication
3. **Input Validation**: Regex patterns for email, phone, username
4. **File Upload**: Validates file type and size
5. **Database**: Uses mysqli (supports prepared statements)

---

## Code Comments

All PHP files include detailed comments explaining:
- What each section does
- How functions work
- Database queries and operations
- Validation logic
- Security measures

### Example Comment Structure
```php
// Brief description of what this section does
$variable = value; // Explanation of why

// Check database query results
if (mysqli_num_rows($result) > 0) {
    // Process results
}
```

---

## Beginner Tips

1. **Start with Login**: First test login functionality
2. **Create Test Posts**: Post something to see it on feed
3. **Test Friends**: Add test users as friends
4. **Try Messaging**: Send messages between two accounts
5. **Leave Comments**: Comment on posts to understand flow
6. **Explore Files**: Read PHP files to understand how it works

---

## Next Steps (Advanced Features)

To enhance the project, consider adding:
1. Image uploads for posts
2. Story feature (24-hour posts)
3. Notification system
4. Search functionality
5. User blocking feature
6. Theme customization
7. Advanced privacy settings
8. Activity logging

---

## Support & Help

If you encounter issues:
1. Read the error message carefully
2. Check the Troubleshooting section
3. Review README.md for detailed explanations
4. Check browser console (F12) for JavaScript errors
5. Verify all folders have correct permissions
6. Make sure MySQL and Apache are running

---

**Installation Last Updated**: February 2026
**Version**: 1.0
**Status**: Ready to Use

