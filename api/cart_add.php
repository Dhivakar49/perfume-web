<?php
/**
 * Add to Cart API
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart', 'redirect' => 'login.php']);
    exit();
}

// Verify CSRF token (optional - skip if not provided)
$csrf_token = $_POST['csrf_token'] ?? '';
if (!empty($csrf_token) && !verifyCSRFToken($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit();
}

// Get input data
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);
$user_id = getUserId();

// Validation
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if product exists and has stock
    $stmt = $db->prepare("SELECT id, name, stock FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }
    
    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
        exit();
    }
    
    // Check if product already in cart
    $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch();
    
    if ($cart_item) {
        // Update quantity
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        if ($new_quantity > $product['stock']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more items than available stock']);
            exit();
        }
        
        $stmt = $db->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_quantity, $cart_item['id']]);
    } else {
        // Insert new cart item
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    
    // Get cart count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetch()['count'];
    
    // Try to log activity (don't fail if it doesn't work)
    try {
        logActivity('add_to_cart', 'product', $product_id, "Added {$product['name']} to cart");
    } catch (Exception $e) {
        error_log("Activity log failed: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully!',
        'cart_count' => $cart_count
    ]);
    
} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart: ' . $e->getMessage()]);
}
exit();
?>
