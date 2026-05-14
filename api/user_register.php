<?php
/**
 * User Registration API
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Log request
error_log("Register API called - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    // Get input data
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    
    error_log("Registration attempt - Name: $name, Email: $email");
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Name, email and password are required']);
        exit();
    }
    
    if (!isValidEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit();
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }
    
    // Attempt registration
    $result = registerUser($name, $email, $password, $phone);
    error_log("Registration result: " . print_r($result, true));
    
    if ($result['success']) {
        // Auto login after registration
        $loginResult = loginUser($email, $password);
        error_log("Auto-login result: " . print_r($loginResult, true));
        
        if ($loginResult['success']) {
            error_log("Registration and login successful - Session ID: " . session_id());
            error_log("Session data: " . print_r($_SESSION, true));
            
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful! Redirecting...',
                'redirect' => 'index.php',
                'user' => [
                    'id' => $_SESSION['user_id'] ?? null,
                    'name' => $_SESSION['user_name'] ?? null,
                    'email' => $_SESSION['user_email'] ?? null
                ]
            ]);
        } else {
            // Registration succeeded but login failed
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful! Please login.',
                'redirect' => 'login.php'
            ]);
        }
    } else {
        error_log("Registration failed: " . $result['message']);
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    error_log("Registration exception: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Registration error: ' . $e->getMessage()
    ]);
}
exit();
?>
