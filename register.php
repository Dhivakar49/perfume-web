<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Register - Perfume Palace';
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
    max-width: 500px;
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

.password-strength {
    height: 4px;
    background: var(--border-color);
    border-radius: 2px;
    margin-top: 8px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0;
    transition: all 0.3s ease;
}

.password-strength-bar.weak {
    width: 33%;
    background: #f44336;
}

.password-strength-bar.medium {
    width: 66%;
    background: #ff9800;
}

.password-strength-bar.strong {
    width: 100%;
    background: #4CAF50;
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}
</style>

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="auth-card" data-aos="fade-up">
                    <div class="auth-header">
                        <h2>Create Account</h2>
                        <p>Join Perfume Palace today</p>
                    </div>
                    
                    <form id="register-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+91 9876543210">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <div class="password-toggle">
                                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                        <i class="far fa-eye" onclick="togglePassword('password')"></i>
                                    </div>
                                    <div class="password-strength">
                                        <div class="password-strength-bar" id="password-strength-bar"></div>
                                    </div>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password *</label>
                                    <div class="password-toggle">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <i class="far fa-eye" onclick="togglePassword('confirm_password')"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Already have an account? <a href="login.php" class="text-primary fw-bold">Login Here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Password strength checker
$('#password').on('input', function() {
    const password = $(this).val();
    const strengthBar = $('#password-strength-bar');
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;
    
    strengthBar.removeClass('weak medium strong');
    
    if (strength <= 2) {
        strengthBar.addClass('weak');
    } else if (strength <= 4) {
        strengthBar.addClass('medium');
    } else {
        strengthBar.addClass('strong');
    }
});

// Form submission
$(document).ready(function() {
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        
        if (password !== confirmPassword) {
            if (typeof showToast === 'function') {
                showToast('Passwords do not match', 'error');
            } else {
                alert('Passwords do not match');
            }
            return;
        }
        
        const formData = $(this).serialize();
        
        if (typeof showLoading === 'function') {
            showLoading();
        }
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Creating Account...');
        
        $.ajax({
            url: 'api/user_register.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Registration response:', response);
                
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }
                
                if (response.success) {
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Registration successful!', 'success');
                    } else {
                        alert('Registration successful!');
                    }
                    
                    // Redirect to homepage
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 500);
                } else {
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Registration failed', 'error');
                    } else {
                        alert(response.message || 'Registration failed');
                    }
                    submitBtn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> Create Account');
                }
            },
            error: function(xhr, status, error) {
                console.error('Registration error:', error);
                console.error('Response:', xhr.responseText);
                
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }
                
                if (typeof showToast === 'function') {
                    showToast('Registration failed. Please try again.', 'error');
                } else {
                    alert('Registration failed. Please try again.');
                }
                
                submitBtn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> Create Account');
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
