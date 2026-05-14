<?php
/**
 * Authentication Middleware
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Require user login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            jsonResponse(['success' => false, 'message' => 'Please login to continue', 'redirect' => 'login.php'], 401);
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            redirect('login.php');
        }
    }
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            jsonResponse(['success' => false, 'message' => 'Admin access required'], 403);
        } else {
            redirect('admin/login.php');
        }
    }
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, name, email, phone, profile_image, status FROM users WHERE id = ?");
        $stmt->execute([getUserId()]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get current admin data
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, email, full_name, role, status FROM admins WHERE id = ?");
        $stmt->execute([getAdminId()]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * User login
 */
function loginUser($email, $password) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Try to log activity (don't fail if it doesn't work)
            try {
                logActivity('user_login', 'user', $user['id'], 'User logged in');
            } catch (Exception $e) {
                error_log("Activity log failed: " . $e->getMessage());
            }
            
            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Invalid email or password'];
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
    }
}

/**
 * Admin login
 */
function loginAdmin($username, $password) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && verifyPassword($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // Update last login
            $updateStmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$admin['id']]);
            
            logActivity('admin_login', 'admin', $admin['id'], 'Admin logged in');
            
            return ['success' => true, 'message' => 'Login successful', 'admin' => $admin];
        }
        
        return ['success' => false, 'message' => 'Invalid username or password'];
    } catch (Exception $e) {
        error_log("Admin login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed'];
    }
}

/**
 * User registration
 */
function registerUser($name, $email, $password, $phone = null) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Insert new user
        $hashed_password = hashPassword($password);
        $verification_token = generateToken();
        
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, phone, verification_token, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'active', NOW())
        ");
        $stmt->execute([$name, $email, $hashed_password, $phone, $verification_token]);
        
        $user_id = $db->lastInsertId();
        
        // Try to log activity (don't fail if it doesn't work)
        try {
            logActivity('user_registration', 'user', $user_id, 'New user registered');
        } catch (Exception $e) {
            error_log("Activity log failed: " . $e->getMessage());
        }
        
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $user_id];
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

/**
 * User logout
 */
function logoutUser() {
    logActivity('user_logout', 'user', getUserId(), 'User logged out');
    
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    
    session_destroy();
}

/**
 * Admin logout
 */
function logoutAdmin() {
    logActivity('admin_logout', 'admin', getAdminId(), 'Admin logged out');
    
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_role']);
    
    session_destroy();
}
?>
