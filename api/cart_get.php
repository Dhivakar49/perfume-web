<?php
/**
 * Get Cart Items API
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Please login to view cart', 'redirect' => 'login.php'], 401);
}

try {
    $db = Database::getInstance()->getConnection();
    $user_id = getUserId();
    
    $stmt = $db->prepare("
        SELECT 
            c.id as cart_id,
            c.quantity,
            p.id,
            p.name,
            p.slug,
            p.price,
            p.sale_price,
            p.stock,
            pi.image_path as image,
            c.name as category_name
        FROM cart c
        INNER JOIN products p ON c.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE c.user_id = ? AND p.status = 'active'
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();
    
    // Calculate totals
    $subtotal = 0;
    foreach ($items as &$item) {
        $price = $item['sale_price'] ?? $item['price'];
        $item['price_display'] = $price;
        $item['subtotal'] = $price * $item['quantity'];
        $subtotal += $item['subtotal'];
    }
    
    $shipping = calculateShipping($subtotal);
    $tax = calculateTax($subtotal);
    $total = $subtotal + $shipping + $tax;
    
    jsonResponse([
        'success' => true,
        'items' => $items,
        'summary' => [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'item_count' => count($items)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Get cart error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to load cart'], 500);
}
?>
