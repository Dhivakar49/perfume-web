<?php
/**
 * Simple Login Status Checker
 * Visit this page to quickly check if you're logged in
 */

require_once __DIR__ . '/bootstrap.php';

$page_title = 'Login Status Check';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .status-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .status-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .logged-in {
            color: #28a745;
        }
        .logged-out {
            color: #dc3545;
        }
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #d4af37;
            color: white;
        }
        .btn-primary:hover {
            background: #b8941f;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .session-data {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="status-box">
        <?php if (isLoggedIn()): ?>
            <div class="status-icon logged-in">✓</div>
            <h1>You Are Logged In!</h1>
            <p>Your session is active and working correctly.</p>
            
            <div class="user-info">
                <h3>User Information:</h3>
                <p><strong>User ID:</strong> <?php echo getUserId(); ?></p>
                <p><strong>Name:</strong> <?php echo $_SESSION['user_name'] ?? 'Not set'; ?></p>
                <p><strong>Email:</strong> <?php echo $_SESSION['user_email'] ?? 'Not set'; ?></p>
                <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            </div>
            
            <div>
                <a href="index.php" class="btn btn-primary">Go to Homepage</a>
                <a href="products.php" class="btn btn-secondary">Browse Products</a>
                <a href="api/logout.php" class="btn btn-danger">Logout</a>
            </div>
            
        <?php else: ?>
            <div class="status-icon logged-out">✗</div>
            <h1>You Are Not Logged In</h1>
            <p>Please login to access your account.</p>
            
            <div>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
                <a href="debug_login.php" class="btn btn-secondary">Debug Login</a>
            </div>
        <?php endif; ?>
        
        <div class="session-data">
            <strong>Session Data:</strong>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><small>
                <a href="test_session.php">Test Session</a> | 
                <a href="debug_login.php">Debug Login</a> | 
                <a href="test_ajax_login.html">Test AJAX Login</a>
            </small></p>
        </div>
    </div>
</body>
</html>
