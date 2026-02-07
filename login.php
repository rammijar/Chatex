<?php
// Chatex Social Media Platform
// User Login File
// Authenticates user credentials

include('include/config.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Clear previous errors
    $error = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    
    // Get form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        // Hash password for comparison
        $hashed_password = md5($password);
        
        // First check if user exists
        $check_query = "SELECT * FROM users WHERE username = '$username' AND password = '$hashed_password'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) == 1) {
            $user_data = mysqli_fetch_assoc($check_result);
            
            // Check if user is approved by admin
            if ($user_data['approved_status'] == 0) {
                $error = "Your account is pending admin approval. Please wait for admin to approve your registration.";
            } else {
                // Login successful
                // Set session variables
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['firstname'] = $user_data['firstname'];
                $_SESSION['lastname'] = $user_data['lastname'];
                
                // Update online status
                $update_query = "UPDATE users SET online_status = 1 WHERE id = " . $user_data['id'];
                mysqli_query($conn, $update_query);
                
                // Redirect to home
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Chatex Login</h1>
            <p>Connect with your friends</p>
            <p style="font-size: 0.85rem; color: #ff9800; background: #fff3cd; padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem;">
                💡 <strong>Note:</strong> New users need admin approval after registration
            </p>
            
            <!-- Display error message -->
            <?php if (!empty($error)): ?>
                <div class="error-box">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Display success message if registration just completed -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-box">
                    <p><?php echo $_SESSION['success']; ?></p>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <!-- Login form -->
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Enter your username">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" name="login" class="btn-primary">Login</button>
            </form>
            
            <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
