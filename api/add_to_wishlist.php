<?php
/**
 * Add to Wishlist API
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Check if user is logged in
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Please login to add items to wishlist', 'redirect' => 'login.php'], 401);
}

// Get input data
$product_id = intval($_POST['product_id'] ?? 0);
$user_id = getUserId();

// Validation
if ($product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid product']);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if product exists
    $stmt = $db->prepare("SELECT id, name FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        jsonResponse(['success' => false, 'message' => 'Product not found']);
    }
    
    // Check if product already in wishlist
    $stmt = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $wishlist_item = $stmt->fetch();
    
    if ($wishlist_item) {
        jsonResponse(['success' => false, 'message' => 'Product already in wishlist']);
    }
    
    // Insert into wishlist
    $stmt = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $product_id]);
    
    // Get wishlist count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $wishlist_count = $stmt->fetch()['count'];
    
    logActivity('add_to_wishlist', 'product', $product_id, "Added {$product['name']} to wishlist");
    
    jsonResponse([
        'success' => true,
        'message' => 'Product added to wishlist',
        'wishlist_count' => $wishlist_count
    ]);
    
} catch (Exception $e) {
    error_log("Add to wishlist error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to add product to wishlist'], 500);
}
?>
