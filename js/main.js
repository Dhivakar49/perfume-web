$(document).ready(function() {
    // Load cart count on page load
    updateCartCount();
    
    // Check login status
    checkLoginStatus();
});

// Update cart count in header
function updateCartCount() {
    $.ajax({
        url: 'api/get_cart.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#cart-count').text(response.count);
                if (response.count > 0) {
                    $('#cart-count').show();
                }
            }
        }
    });
}

// Check if user is logged in
function checkLoginStatus() {
    // This would be handled by PHP session
    // Update UI based on login status
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = $('<div>')
        .addClass('notification')
        .addClass(type)
        .text(message)
        .appendTo('body');
    
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Add to cart function
function addToCart(productId) {
    $.ajax({
        url: 'api/add_to_cart.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            product_id: productId,
            quantity: 1
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Added to cart!', 'success');
                updateCartCount();
            } else {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    showNotification(response.message, 'error');
                }
            }
        },
        error: function() {
            showNotification('Failed to add to cart', 'error');
        }
    });
}
