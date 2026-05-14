<?php
require_once __DIR__ . '/bootstrap.php';

// Require login
requireLogin();

$page_title = 'Order Confirmation - Perfume Palace';

// Get order ID
$order_id = intval($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    redirect('index.php');
}

// Get order details
try {
    $db = Database::getInstance()->getConnection();
    $user_id = getUserId();
    
    $stmt = $db->prepare("
        SELECT o.*, sa.full_name, sa.phone, sa.email, sa.address_line1, sa.address_line2, sa.city, sa.state, sa.pincode, sa.country
        FROM orders o
        JOIN shipping_addresses sa ON o.shipping_address_id = sa.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        redirect('index.php');
    }
    
    // Get order items
    $stmt = $db->prepare("
        SELECT oi.*, p.name, p.slug, pi.image_path
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Order confirmation error: " . $e->getMessage());
    redirect('index.php');
}

include 'includes/header.php';
?>

<style>
.confirmation-page {
    padding: 60px 0;
    background: #f8f9fa;
    min-height: 80vh;
}

.confirmation-card {
    background: white;
    border-radius: 15px;
    padding: 50px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    text-align: center;
    max-width: 800px;
    margin: 0 auto 40px;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: #d4edda;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
}

.success-icon i {
    font-size: 50px;
    color: #28a745;
}

.confirmation-card h1 {
    font-size: 32px;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 15px;
}

.order-number {
    font-size: 20px;
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 10px;
}

.confirmation-message {
    color: var(--text-light);
    font-size: 16px;
    margin-bottom: 30px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.order-details {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    max-width: 800px;
    margin: 0 auto;
}

.order-details h3 {
    font-size: 24px;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--primary-color);
}

.detail-section {
    margin-bottom: 30px;
}

.detail-section h4 {
    font-size: 18px;
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 15px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.order-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.order-item-image {
    width: 80px;
    height: 80px;
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
    margin-bottom: 5px;
}

.order-item-qty {
    color: var(--text-light);
    font-size: 14px;
}

.order-item-price {
    font-weight: 600;
    color: var(--primary-color);
}
</style>

<div class="confirmation-page">
    <div class="container">
        <div class="confirmation-card" data-aos="fade-up">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Order Placed Successfully!</h1>
            <div class="order-number">Order #<?php echo htmlspecialchars($order['order_number']); ?></div>
            <p class="confirmation-message">
                Thank you for your order! We've received your order and will process it shortly.<br>
                A confirmation email has been sent to <strong><?php echo htmlspecialchars($order['email']); ?></strong>
            </p>
            <div class="action-buttons">
                <a href="orders.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-box"></i> View My Orders
                </a>
                <a href="products.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>

        <div class="order-details" data-aos="fade-up" data-aos-delay="100">
            <h3>Order Details</h3>
            
            <div class="detail-section">
                <h4><i class="fas fa-box"></i> Order Information</h4>
                <div class="detail-row">
                    <span>Order Number</span>
                    <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Order Date</span>
                    <strong><?php echo date('F d, Y', strtotime($order['created_at'])); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Payment Method</span>
                    <strong><?php echo strtoupper($order['payment_method']); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Order Status</span>
                    <strong class="text-warning"><?php echo ucfirst($order['order_status']); ?></strong>
                </div>
            </div>

            <div class="detail-section">
                <h4><i class="fas fa-shipping-fast"></i> Shipping Address</h4>
                <p>
                    <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['address_line1']); ?><br>
                    <?php if ($order['address_line2']): ?>
                        <?php echo htmlspecialchars($order['address_line2']); ?><br>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?><br>
                    <?php echo htmlspecialchars($order['country']); ?><br>
                    Phone: <?php echo htmlspecialchars($order['phone']); ?>
                </p>
            </div>

            <div class="detail-section">
                <h4><i class="fas fa-shopping-bag"></i> Order Items</h4>
                <?php foreach ($order_items as $item): ?>
                    <?php $image = $item['image_path'] ?? 'assets/images/products/default.jpg'; ?>
                    <div class="order-item">
                        <div class="order-item-image">
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="order-item-details">
                            <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="order-item-qty">Quantity: <?php echo $item['quantity']; ?></div>
                        </div>
                        <div class="order-item-price">
                            <?php echo formatPrice($item['subtotal']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="detail-section">
                <h4><i class="fas fa-receipt"></i> Order Summary</h4>
                <div class="detail-row">
                    <span>Subtotal</span>
                    <strong><?php echo formatPrice($order['subtotal']); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Shipping</span>
                    <strong><?php echo $order['shipping_charge'] > 0 ? formatPrice($order['shipping_charge']) : 'FREE'; ?></strong>
                </div>
                <div class="detail-row">
                    <span>Tax</span>
                    <strong><?php echo formatPrice($order['tax_amount']); ?></strong>
                </div>
                <div class="detail-row" style="border-top: 2px solid var(--primary-color); padding-top: 15px; margin-top: 10px; font-size: 20px;">
                    <span>Total</span>
                    <strong style="color: var(--primary-color);"><?php echo formatPrice($order['total_amount']); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
