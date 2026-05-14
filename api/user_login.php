<?php
/**
 * User Login API - Debug Version
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Log the request
error_log("Login API called - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get input data
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

error_log("Login attempt - Email: $email");

// Validation
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

if (!isValidEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Attempt login
try {
    $result = loginUser($email, $password);
    error_log("Login result: " . print_r($result, true));
    
    if ($result['success']) {
        error_log("Login successful - Session ID: " . session_id());
        error_log("Session data: " . print_r($_SESSION, true));
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful! Redirecting...',
            'redirect' => 'index.php',
            'user' => [
                'id' => $_SESSION['user_id'] ?? null,
                'name' => $_SESSION['user_name'] ?? null,
                'email' => $_SESSION['user_email'] ?? null
            ],
            'session_id' => session_id(),
            'debug' => [
                'session_started' => true,
                'session_id' => session_id()
            ]
        ]);
    } else {
        error_log("Login failed: " . $result['message']);
        echo json_encode($result);
    }
} catch (Exception $e) {
    error_log("Login exception: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Login error: ' . $e->getMessage()
    ]);
}
exit();
?>
