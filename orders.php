<?php
require_once __DIR__ . '/bootstrap.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Orders - Perfume Palace';
$user_id = getUserId();

// Fetch user orders
try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("
        SELECT o.*, 
               COUNT(oi.id) as item_count,
               SUM(oi.quantity) as total_items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Orders page error: " . $e->getMessage());
    $orders = [];
}

include 'includes/header.php';
?>

<div class="container" style="padding: 60px 15px;">
    <h1 style="margin-bottom: 30px; font-size: 32px; font-weight: 700;">My Orders</h1>
    
    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px;">
            <i class="fas fa-box-open" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">No Orders Yet</h3>
            <p style="color: #999; margin-bottom: 30px;">You haven't placed any orders yet.</p>
            <a href="products.php" class="btn btn-primary" style="padding: 12px 30px; background: #d4af37; color: white; border: none; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600;">
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach ($orders as $order): ?>
                <div style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">
                                Order #<?php echo htmlspecialchars($order['order_number']); ?>
                            </h3>
                            <p style="color: #666; font-size: 14px; margin: 0;">
                                <i class="far fa-calendar"></i> 
                                <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="display: inline-block; padding: 6px 16px; background: <?php 
                                echo $order['status'] === 'completed' ? '#28a745' : 
                                    ($order['status'] === 'pending' ? '#ffc107' : 
                                    ($order['status'] === 'processing' ? '#17a2b8' : '#6c757d')); 
                            ?>; color: white; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </div>
                            <p style="font-size: 24px; font-weight: 700; color: #d4af37; margin: 0;">
                                ₹<?php echo number_format($order['total_amount'], 2); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 15px; margin-top: 15px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                            <div>
                                <p style="color: #999; font-size: 13px; margin-bottom: 5px;">Items</p>
                                <p style="font-weight: 600; margin: 0;"><?php echo $order['total_items']; ?> item(s)</p>
                            </div>
                            <div>
                                <p style="color: #999; font-size: 13px; margin-bottom: 5px;">Payment Method</p>
                                <p style="font-weight: 600; margin: 0; text-transform: capitalize;">
                                    <?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?>
                                </p>
                            </div>
                            <div>
                                <p style="color: #999; font-size: 13px; margin-bottom: 5px;">Payment Status</p>
                                <p style="font-weight: 600; margin: 0; text-transform: capitalize; color: <?php echo $order['payment_status'] === 'paid' ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo htmlspecialchars($order['payment_status'] ?? 'Pending'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                               style="padding: 10px 20px; background: #d4af37; color: white; border: none; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                                View Details
                            </a>
                            <?php if ($order['status'] === 'pending'): ?>
                                <button onclick="cancelOrder(<?php echo $order['id']; ?>)" 
                                        style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                                    Cancel Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        // Add cancel order logic here
        alert('Order cancellation feature coming soon!');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
