<?php
require_once __DIR__ . '/bootstrap.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Profile - Perfume Palace';
$user_id = getUserId();

// Fetch user data
try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header('Location: logout.php');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Profile page error: " . $e->getMessage());
    die("Error loading profile");
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    
    try {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $user_id]);
        
        $success_message = "Profile updated successfully!";
        
        // Refresh user data
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
    } catch (Exception $e) {
        $error_message = "Failed to update profile: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container" style="padding: 60px 15px;">
    <h1 style="margin-bottom: 30px; font-size: 32px; font-weight: 700;">My Profile</h1>
    
    <?php if (isset($success_message)): ?>
        <div style="padding: 15px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 20px;">
            ✓ <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div style="padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 20px;">
            ✗ <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
        <!-- Profile Sidebar -->
        <div>
            <div style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 30px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: 700;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 5px;">
                    <?php echo htmlspecialchars($user['name']); ?>
                </h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>
                <p style="color: #999; font-size: 13px;">
                    Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
            </div>
            
            <div style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 15px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="orders.php" style="color: #666; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px; transition: all 0.3s;">
                            <i class="fas fa-box"></i> My Orders
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="wishlist.php" style="color: #666; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px; transition: all 0.3s;">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" style="color: #dc3545; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px; transition: all 0.3s;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Profile Form -->
        <div>
            <div style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Personal Information</h2>
                
                <form method="POST" action="">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <button type="submit" name="update_profile"
                            style="padding: 12px 30px; background: #d4af37; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;">
                        Update Profile
                    </button>
                </form>
            </div>
            
            <div style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 30px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Change Password</h2>
                
                <form method="POST" action="change-password.php">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Current Password</label>
                        <input type="password" name="current_password" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">New Password</label>
                        <input type="password" name="new_password" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Confirm New Password</label>
                        <input type="password" name="confirm_password" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <button type="submit" name="change_password"
                            style="padding: 12px 30px; background: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
