<?php
require_once __DIR__ . '/bootstrap.php';

// Require login
requireLogin();

$page_title = 'Shopping Cart - Perfume Palace';
include 'includes/header.php';

// Get cart items
$cart_items = [];
$subtotal = 0;

try {
    $db = Database::getInstance()->getConnection();
    $user_id = getUserId();
    
    $stmt = $db->prepare("
        SELECT c.*, p.name, p.price, p.sale_price, p.slug, p.stock,
               pi.image_path, b.name as brand_name
        FROM cart c
        JOIN products p ON c.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE c.user_id = ? AND p.status = 'active'
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // Calculate subtotal
    foreach ($cart_items as $item) {
        $price = $item['sale_price'] ?? $item['price'];
        $subtotal += $price * $item['quantity'];
    }
    
} catch (Exception $e) {
    error_log("Cart error: " . $e->getMessage());
}

$shipping = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
$tax = calculateTax($subtotal);
$total = $subtotal + $shipping + $tax;
?>

<style>
.cart-page {
    padding: 60px 0;
    background: #f8f9fa;
    min-height: 80vh;
}

.cart-header {
    margin-bottom: 40px;
}

.cart-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 10px;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.cart-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
    margin-bottom: 40px;
}

.cart-items {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.cart-item {
    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image {
    width: 120px;
    height: 120px;
    border-radius: 10px;
    overflow: hidden;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-details {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.cart-item-name {
    font-size: 18px;
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 5px;
}

.cart-item-brand {
    color: var(--text-light);
    font-size: 14px;
    margin-bottom: 10px;
}

.cart-item-price {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-color);
}

.cart-item-actions {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-end;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
}

.quantity-control button {
    width: 32px;
    height: 32px;
    border: none;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 18px;
    color: var(--secondary-color);
    transition: var(--transition);
}

.quantity-control button:hover {
    background: var(--primary-color);
    color: white;
}

.quantity-control input {
    width: 50px;
    text-align: center;
    border: none;
    background: transparent;
    font-weight: 600;
}

.remove-btn {
    color: #dc3545;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    padding: 5px 10px;
    transition: var(--transition);
}

.remove-btn:hover {
    color: #c82333;
}

.cart-summary {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 100px;
}

.cart-summary h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--secondary-color);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.summary-row.total {
    border-top: 2px solid var(--primary-color);
    border-bottom: none;
    padding-top: 20px;
    margin-top: 10px;
    font-size: 20px;
    font-weight: 700;
    color: var(--secondary-color);
}

.free-shipping-badge {
    background: #d4edda;
    color: #155724;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    margin: 15px 0;
    text-align: center;
}

.checkout-btn {
    width: 100%;
    padding: 15px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 20px;
    transition: var(--transition);
}

.checkout-btn:hover {
    background: #b8941f;
    transform: translateY(-2px);
}

.continue-shopping {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: var(--text-light);
    text-decoration: none;
}

.continue-shopping:hover {
    color: var(--primary-color);
}

.empty-cart {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 15px;
}

.empty-cart i {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-cart h2 {
    font-size: 28px;
    color: var(--secondary-color);
    margin-bottom: 15px;
}

.empty-cart p {
    color: var(--text-light);
    margin-bottom: 30px;
}

@media (max-width: 992px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 15px;
    }
    
    .cart-item-image {
        width: 80px;
        height: 80px;
    }
    
    .cart-item-actions {
        grid-column: 1 / -1;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}
</style>

<div class="cart-page">
    <div class="container">
        <div class="cart-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Shopping Cart</li>
                </ol>
            </nav>
            <h1>Shopping Cart</h1>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your Cart is Empty</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                        $price = $item['sale_price'] ?? $item['price'];
                        $item_total = $price * $item['quantity'];
                        $image = $item['image_path'] ?? 'assets/images/products/default.jpg';
                        ?>
                        <div class="cart-item" data-cart-id="<?php echo $item['id']; ?>">
                            <div class="cart-item-image">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            
                            <div class="cart-item-details">
                                <div>
                                    <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <?php if ($item['brand_name']): ?>
                                        <div class="cart-item-brand"><?php echo htmlspecialchars($item['brand_name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="cart-item-price"><?php echo formatPrice($price); ?></div>
                            </div>
                            
                            <div class="cart-item-actions">
                                <div class="quantity-control">
                                    <button onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" readonly>
                                    <button onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button class="remove-btn" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span><?php echo $shipping > 0 ? formatPrice($shipping) : 'FREE'; ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (<?php echo TAX_PERCENTAGE; ?>%)</span>
                        <span><?php echo formatPrice($tax); ?></span>
                    </div>
                    
                    <?php if ($shipping == 0 && $subtotal > 0): ?>
                        <div class="free-shipping-badge">
                            <i class="fas fa-truck"></i> You've got FREE shipping!
                        </div>
                    <?php elseif ($subtotal > 0): ?>
                        <div class="free-shipping-badge" style="background: #fff3cd; color: #856404;">
                            <i class="fas fa-info-circle"></i> Add <?php echo formatPrice(FREE_SHIPPING_THRESHOLD - $subtotal); ?> more for FREE shipping
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                    
                    <button class="checkout-btn" onclick="proceedToCheckout()">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </button>
                    
                    <a href="products.php" class="continue-shopping">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(cartId);
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'api/update_cart.php',
        method: 'POST',
        data: {
            cart_id: cartId,
            quantity: newQuantity,
            csrf_token: CSRF_TOKEN
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                location.reload();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showToast('Failed to update quantity', 'error');
        }
    });
}

function removeFromCart(cartId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'api/remove_from_cart.php',
        method: 'POST',
        data: {
            cart_id: cartId,
            csrf_token: CSRF_TOKEN
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast('Item removed from cart', 'success');
                location.reload();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showToast('Failed to remove item', 'error');
        }
    });
}

function proceedToCheckout() {
    window.location.href = 'checkout.php';
}
</script>
