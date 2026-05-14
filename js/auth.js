// Login form handler
$('#login-form').on('submit', function(e) {
    e.preventDefault();
    
    const email = $('#email').val();
    const password = $('#password').val();
    
    $.ajax({
        url: 'api/login.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ email, password }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Login successful!', 'success');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            showNotification('Login failed', 'error');
        }
    });
});

// Register form handler
$('#register-form').on('submit', function(e) {
    e.preventDefault();
    
    const name = $('#name').val();
    const email = $('#email').val();
    const password = $('#password').val();
    const phone = $('#phone').val();
    
    $.ajax({
        url: 'api/register.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ name, email, password, phone }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Registration successful!', 'success');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            showNotification('Registration failed', 'error');
        }
    });
});
