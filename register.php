<?php
// Chatex Social Media Platform
// User Registration File
// Allows new users to create an account

include('include/config.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$errors = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    
    // Get form data
    $username = trim($_POST['username']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate username - only alphanumeric and underscore
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = "Username must be 3-20 characters, alphanumeric only";
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    // Check if passwords match
    if ($password != $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate phone number (basic validation)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be 10 digits";
    }
    
    // Check if username already exists
    $check_username = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_username);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Username already taken";
    }
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email already registered";
    }
    
    // Handle profile picture upload
    $profile_pic = 'default_user.png';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            if ($_FILES['profile_pic']['size'] < 2000000) { // 2MB limit
                $new_filename = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'profile/' . $new_filename)) {
                    $profile_pic = $new_filename;
                } else {
                    $errors[] = "Failed to upload profile picture";
                }
            } else {
                $errors[] = "Profile picture size must be less than 2MB";
            }
        } else {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed";
        }
    }
    
    // If no errors, insert user into database
    if (count($errors) == 0) {
        // Hash password
        $hashed_password = md5($password);
        
        // Insert user (approved_status = 0 means pending admin approval)
        $insert_query = "INSERT INTO users (username, firstname, lastname, email, phone, password, profile_pic, approved_status) 
                        VALUES ('$username', '$firstname', '$lastname', '$email', '$phone', '$hashed_password', '$profile_pic', 0)";
        
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success'] = "Registration successful! Please wait for admin approval before logging in.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Create Account</h1>
            <p>Join Chatex and connect with friends</p>
            
            <!-- Display errors -->
            <?php if (count($errors) > 0): ?>
                <div class="error-box">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Registration form -->
            <form method="POST" enctype="multipart/form-data">
                <div style="background: #e8f5e9; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border-left: 4px solid #4CAF50;">
                    <p style="margin: 0; color: #2e7d32; font-size: 0.9rem;">
                        📝 <strong>After registration</strong>, your account will be pending admin approval. You'll be able to login once approved!
                    </p>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Choose a username">
                </div>
                
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstname" required placeholder="First name">
                </div>
                
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastname" required placeholder="Last name">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Email address">
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" required placeholder="10 digit phone number">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters">
                </div>
                
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm password">
                </div>
                
                <div class="form-group">
                    <label>Profile Picture (Optional)</label>
                    <input type="file" name="profile_pic" accept="image/*">
                </div>
                
                <button type="submit" name="register" class="btn-primary">Register</button>
            </form>
            
            <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
