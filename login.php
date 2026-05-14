<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Login - Perfume Palace';
include 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}
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
    color: var(--secondary-color);
    margin-bottom: 10px;
}

.auth-header p {
    color: var(--text-light);
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
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: var(--transition);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.password-toggle {
    position: relative;
}

.password-toggle i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--text-light);
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.divider {
    text-align: center;
    margin: 20px 0;
    position: relative;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--border-color);
}

.divider span {
    background: white;
    padding: 0 15px;
    position: relative;
    color: var(--text-light);
}
</style>

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card" data-aos="fade-up">
                    <div class="auth-header">
                        <h2>Welcome Back</h2>
                        <p>Login to your account</p>
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">
                            <a href="test_real_login.php" style="color: #d4af37;">Don't have credentials? Click here to see all users</a>
                        </p>
                    </div>
                    
                    <form id="login-form">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-toggle">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="far fa-eye" onclick="togglePassword('password')"></i>
                            </div>
                        </div>
                        
                        <div class="form-group d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="forgot-password.php" class="text-primary">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php" class="text-primary fw-bold">Register Now</a></p>
                        <p style="margin-top: 10px;"><a href="register_simple.php" style="color: #666;">Or use Simple Register (No AJAX)</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        
        // Show loading
        if (typeof showLoading === 'function') {
            showLoading();
        }
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Logging in...');
        
        $.ajax({
            url: 'api/user_login.php',
            method: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                console.log('Login response:', response);
                
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }
                
                if (response.success) {
                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Login successful!', 'success');
                    } else {
                        alert('Login successful!');
                    }
                    
                    // Redirect to homepage
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 500);
                } else {
                    // Show error message
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Login failed', 'error');
                    } else {
                        alert(response.message || 'Login failed');
                    }
                    submitBtn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Login');
                }
            },
            error: function(xhr, status, error) {
                console.error('Login error:', error);
                console.error('Response:', xhr.responseText);
                
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }
                
                if (typeof showToast === 'function') {
                    showToast('Login failed. Please try again.', 'error');
                } else {
                    alert('Login failed. Please try again.');
                }
                
                submitBtn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Login');
            }
        });
    });
});

function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
