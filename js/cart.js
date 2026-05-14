$(document).ready(function() {
    loadCart();
});

function loadCart() {
    $.ajax({
        url: 'api/get_cart.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCart(response.cart, response.total);
            } else {
                window.location.href = 'login.php';
            }
        }
    });
}

function displayCart(items, total) {
    const cartItems = $('#cart-items');
    cartItems.empty();
    
    if (items.length === 0) {
        cartItems.html('<p class="empty-cart">Your cart is empty</p>');
        $('#cart-total').text('₹0.00');
        return;
    }
    
    items.forEach(function(item) {
        const row = `
            <div class="cart-item" data-cart-id="${item.cart_id}">
                <img src="${item.image}" alt="${item.name}">
                <div class="item-details">
                    <h3>${item.name}</h3>
                    <p class="price">₹${parseFloat(item.price).toFixed(2)}</p>
                </div>
                <div class="quantity-controls">
                    <button onclick="updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
                </div>
                <p class="item-total">₹${(item.price * item.quantity).toFixed(2)}</p>
                <button class="remove-btn" onclick="removeFromCart(${item.cart_id})">Remove</button>
            </div>
        `;
        cartItems.append(row);
    });
    
    $('#cart-total').text('₹' + total.toFixed(2));
}

function updateQuantity(cartId, quantity) {
    if (quantity < 1) return;
    
    $.ajax({
        url: 'api/update_cart.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            cart_id: cartId,
            quantity: quantity
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadCart();
            }
        }
    });
}

function removeFromCart(cartId) {
    $.ajax({
        url: 'api/remove_from_cart.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            cart_id: cartId
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Item removed', 'success');
                loadCart();
                updateCartCount();
            }
        }
    });
}

function checkout() {
    $.ajax({
        url: 'api/checkout.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Order placed successfully!', 'success');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 2000);
            } else {
                showNotification(response.message, 'error');
            }
        }
    });
}
