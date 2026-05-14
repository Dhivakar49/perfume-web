<?php
require_once __DIR__ . '/bootstrap.php';

// Require login
requireLogin();

$page_title = 'Checkout - Perfume Palace';
include 'includes/header.php';

// Get cart items
$cart_items = [];
$subtotal = 0;

try {
    $db = Database::getInstance()->getConnection();
    $user_id = getUserId();
    
    // Get cart items
    $stmt = $db->prepare("
        SELECT c.*, p.name, p.price, p.sale_price, p.slug, p.stock,
               pi.image_path
        FROM cart c
        JOIN products p ON c.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE c.user_id = ? AND p.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // Calculate subtotal
    foreach ($cart_items as $item) {
        $price = $item['sale_price'] ?? $item['price'];
        $subtotal += $price * $item['quantity'];
    }
    
    // Get user details
    $user = getCurrentUser();
    
} catch (Exception $e) {
    error_log("Checkout error: " . $e->getMessage());
}

// Redirect if cart is empty
if (empty($cart_items)) {
    redirect('cart.php');
}

$shipping = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
$tax = calculateTax($subtotal);
$total = $subtotal + $shipping + $tax;
?>

<style>
.checkout-page {
    padding: 60px 0;
    background: #f8f9fa;
    min-height: 80vh;
}

.checkout-header {
    margin-bottom: 40px;
}

.checkout-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: var(--secondary-color);
}

.checkout-steps {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    gap: 20px;
}

.step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.step.active {
    background: var(--primary-color);
    color: white;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.step.active .step-number {
    background: white;
    color: var(--primary-color);
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

.checkout-form {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.form-section {
    margin-bottom: 30px;
}

.form-section h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--secondary-color);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.payment-methods {
    display: grid;
    gap: 15px;
}

.payment-method {
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 10px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 15px;
}

.payment-method:hover {
    border-color: var(--primary-color);
}

.payment-method input[type="radio"] {
    width: 20px;
    height: 20px;
}

.payment-method.selected {
    border-color: var(--primary-color);
    background: #fffbf0;
}

.order-summary {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 100px;
}

.order-summary h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--secondary-color);
}

.order-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.order-item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
}

.order-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-item-details {
    flex: 1;
}

.order-item-name {
    font-weight: 600;
    color: var(--secondary-color);
    font-size: 14px;
    margin-bottom: 5px;
}

.order-item-qty {
    color: var(--text-light);
    font-size: 13px;
}

.order-item-price {
    font-weight: 600;
    color: var(--primary-color);
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

.place-order-btn {
    width: 100%;
    padding: 18px;
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

.place-order-btn:hover {
    background: #b8941f;
    transform: translateY(-2px);
}

.secure-checkout {
    text-align: center;
    margin-top: 15px;
    color: var(--text-light);
    font-size: 14px;
}

@media (max-width: 992px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        position: static;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="checkout-page">
    <div class="container">
        <div class="checkout-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
            <h1>Checkout</h1>
        </div>

        <div class="checkout-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <span>Shipping</span>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <span>Payment</span>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <span>Review</span>
            </div>
        </div>

        <div class="checkout-content">
            <div class="checkout-form">
                <form id="checkout-form">
                    <!-- Shipping Information -->
                    <div class="form-section">
                        <h3><i class="fas fa-shipping-fast"></i> Shipping Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Address Line 1 *</label>
                            <input type="text" name="address_line1" placeholder="House No., Building Name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" name="address_line2" placeholder="Road Name, Area, Colony">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" name="city" required>
                            </div>
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" name="state" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>PIN Code *</label>
                                <input type="text" name="pincode" pattern="[0-9]{6}" placeholder="000000" required>
                            </div>
                            <div class="form-group">
                                <label>Country *</label>
                                <input type="text" name="country" value="India" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                        
                        <div class="payment-methods">
                            <label class="payment-method selected">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div>
                                    <strong>Cash on Delivery</strong>
                                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Pay when you receive</p>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="online">
                                <div>
                                    <strong>Online Payment</strong>
                                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Credit/Debit Card, UPI, Net Banking</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="form-section">
                        <h3><i class="fas fa-sticky-note"></i> Order Notes (Optional)</h3>
                        <div class="form-group">
                            <textarea name="order_notes" rows="4" placeholder="Any special instructions for delivery?"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                
                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                        $price = $item['sale_price'] ?? $item['price'];
                        $image = $item['image_path'] ?? 'assets/images/products/default.jpg';
                        ?>
                        <div class="order-item">
                            <div class="order-item-image">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="order-item-details">
                                <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="order-item-qty">Qty: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div class="order-item-price">
                                <?php echo formatPrice($price * $item['quantity']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-row">
                    <span>Subtotal</span>
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
                
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                
                <button type="button" class="place-order-btn" onclick="placeOrder()">
                    <i class="fas fa-lock"></i> Place Order
                </button>
                
                <div class="secure-checkout">
                    <i class="fas fa-shield-alt"></i> Secure Checkout
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Payment method selection
$('.payment-method').on('click', function() {
    $('.payment-method').removeClass('selected');
    $(this).addClass('selected');
    $(this).find('input[type="radio"]').prop('checked', true);
});

function placeOrder() {
    // Validate form
    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get form data
    const formData = new FormData(form);
    formData.append('csrf_token', CSRF_TOKEN);
    
    showLoading();
    
    $.ajax({
        url: 'api/place_order.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                showToast('Order placed successfully!', 'success');
                
                // Redirect to order confirmation
                setTimeout(function() {
                    window.location.href = 'order-confirmation.php?order_id=' + response.order_id;
                }, 1000);
            } else {
                showToast(response.message || 'Failed to place order', 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error('Order error:', xhr.responseText);
            showToast('Failed to place order. Please try again.', 'error');
        }
    });
}
</script>
