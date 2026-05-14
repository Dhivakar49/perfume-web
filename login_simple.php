<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Simple Login - Perfume Palace';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Handle form submission
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Attempt login
        $result = loginUser($email, $password);
        
        if ($result['success']) {
            // Redirect to homepage
            redirect('index.php');
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<style>
.auth-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.auth-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    max-width: 450px;
    width: 100%;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h2 {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
}

.form-control:focus {
    border-color: #d4af37;
    outline: none;
}

.btn-submit {
    width: 100%;
    padding: 15px;
    background: #d4af37;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}

.btn-submit:hover {
    background: #b8941f;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}
</style>

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2>Welcome Back</h2>
                        <p>Simple Login (No AJAX)</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn-submit">Login</button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register_simple.php" style="color: #d4af37; font-weight: bold;">Register Now</a></p>
                        <p><a href="login.php" style="color: #666;">Use AJAX Version</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
