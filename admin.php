<?php
// Chatex Social Media Platform
// Admin Panel - User Management
// Only accessible by admin user

include('include/config.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if user is admin (username = 'admin')
if ($user['username'] != 'admin') {
    die("<h2>Access Denied!</h2><p>Only admin can access this panel.</p><p><a href='index.php'>Go Back to Home</a></p>");
}

$message = '';

// Handle user approval
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_user'])) {
    $pending_user_id = $_POST['user_id'];
    
    $update_query = "UPDATE users SET approved_status = 1 WHERE id = '$pending_user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "User approved successfully!";
    } else {
        $message = "Failed to approve user";
    }
}

// Handle user rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_user'])) {
    $pending_user_id = $_POST['user_id'];
    
    $delete_query = "DELETE FROM users WHERE id = '$pending_user_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $message = "User rejected and deleted!";
    } else {
        $message = "Failed to reject user";
    }
}

// Handle user deletion (for approved users)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $target_user_id = $_POST['user_id'];
    
    // Prevent deleting admin
    if ($target_user_id != 1) { // Assuming admin has id=1
        $delete_query = "DELETE FROM users WHERE id = '$target_user_id'";
        
        if (mysqli_query($conn, $delete_query)) {
            $message = "User deleted successfully!";
        } else {
            $message = "Failed to delete user";
        }
    } else {
        $message = "Cannot delete admin user!";
    }
}

// Get pending users (not approved)
$pending_query = "SELECT * FROM users WHERE approved_status = 0 ORDER BY registration_date DESC";
$pending_result = mysqli_query($conn, $pending_query);

// Get all approved users
$approved_query = "SELECT * FROM users WHERE approved_status = 1 ORDER BY registration_date DESC";
$approved_result = mysqli_query($conn, $approved_query);

// Get total statistics
$total_users_query = "SELECT COUNT(*) as total FROM users";
$total_result = mysqli_query($conn, $total_users_query);
$total_data = mysqli_fetch_assoc($total_result);

$pending_users_query = "SELECT COUNT(*) as pending FROM users WHERE approved_status = 0";
$pending_count_result = mysqli_query($conn, $pending_users_query);
$pending_count = mysqli_fetch_assoc($pending_count_result);

$approved_users_query = "SELECT COUNT(*) as approved FROM users WHERE approved_status = 1";
$approved_count_result = mysqli_query($conn, $approved_users_query);
$approved_count = mysqli_fetch_assoc($approved_count_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Chatex</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #4CAF50, #2196F3);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 2rem;
        }
        
        .admin-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
        }
        
        .admin-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .admin-section h2 {
            color: #4CAF50;
            margin-top: 0;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 0.5rem;
        }
        
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .user-table th {
            background-color: #f5f5f5;
            padding: 1rem;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        
        .user-table td {
            padding: 1rem;
            border-bottom: 1px solid #ddd;
        }
        
        .user-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-online {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-offline {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-buttons form {
            display: inline;
        }
        
        .action-buttons button {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-approve {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-approve:hover {
            background-color: #45a049;
        }
        
        .btn-reject {
            background-color: #f44336;
            color: white;
        }
        
        .btn-reject:hover {
            background-color: #da190b;
        }
        
        .btn-delete {
            background-color: #ff9800;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #e68900;
        }
        
        .empty-message {
            text-align: center;
            color: #999;
            padding: 2rem;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #4CAF50;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="admin-container">
        <!-- Back Button -->
        <a href="index.php" class="back-link">← Back to Home</a>
        
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>👨‍💼 Admin Panel</h1>
            <p>Manage users, approve registrations, and monitor platform activity</p>
        </div>
        
        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <div class="message-box">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number"><?php echo $total_data['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending Approval</h3>
                <div class="number" style="color: #ff9800;"><?php echo $pending_count['pending']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Approved Users</h3>
                <div class="number" style="color: #4CAF50;"><?php echo $approved_count['approved']; ?></div>
            </div>
        </div>
        
        <!-- Pending User Approvals Section -->
        <div class="admin-section">
            <h2>⏳ Pending User Registrations (<?php echo mysqli_num_rows($pending_result); ?>)</h2>
            
            <?php if (mysqli_num_rows($pending_result) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pending_user = mysqli_fetch_assoc($pending_result)): ?>
                                <tr>
                                    <td><?php echo $pending_user['id']; ?></td>
                                    <td><strong><?php echo $pending_user['username']; ?></strong></td>
                                    <td><?php echo $pending_user['firstname'] . ' ' . $pending_user['lastname']; ?></td>
                                    <td><?php echo $pending_user['email']; ?></td>
                                    <td><?php echo $pending_user['phone']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($pending_user['registration_date'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Approve Button -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $pending_user['id']; ?>">
                                                <button type="submit" name="approve_user" class="btn-approve" onclick="return confirm('Approve this user?')">✓ Approve</button>
                                            </form>
                                            
                                            <!-- Reject Button -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $pending_user['id']; ?>">
                                                <button type="submit" name="reject_user" class="btn-reject" onclick="return confirm('Reject this user? This action cannot be undone.')">✕ Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-message">
                    <p>✓ No pending registrations. All users have been approved!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Approved Users Section -->
        <div class="admin-section">
            <h2>✓ Approved Users (<?php echo mysqli_num_rows($approved_result); ?>)</h2>
            
            <?php if (mysqli_num_rows($approved_result) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($approved_user = mysqli_fetch_assoc($approved_result)): ?>
                                <tr>
                                    <td><?php echo $approved_user['id']; ?></td>
                                    <td><strong><?php echo $approved_user['username']; ?></strong></td>
                                    <td><?php echo $approved_user['firstname'] . ' ' . $approved_user['lastname']; ?></td>
                                    <td><?php echo $approved_user['email']; ?></td>
                                    <td><?php echo $approved_user['phone']; ?></td>
                                    <td>
                                        <?php if ($approved_user['online_status'] == 1): ?>
                                            <span class="status-badge status-online">🟢 Online</span>
                                        <?php else: ?>
                                            <span class="status-badge status-offline">🔴 Offline</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($approved_user['registration_date'])); ?></td>
                                    <td>
                                        <?php if ($approved_user['username'] != 'admin'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $approved_user['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn-delete" onclick="return confirm('Delete this user? This action cannot be undone.')">🗑️ Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #999;">Admin Account</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-message">
                    <p>No approved users yet.</p>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
    <?php include('include/footer.php'); ?>
</body>
</html>
