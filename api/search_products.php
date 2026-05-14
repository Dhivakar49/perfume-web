<?php
/**
 * Search Products API
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$query = sanitize($_GET['q'] ?? '');

if (strlen($query) < 2) {
    jsonResponse(['success' => true, 'products' => []]);
}

try {
    $db = Database::getInstance()->getConnection();
    
    $search_term = "%{$query}%";
    
    $stmt = $db->prepare("
        SELECT 
            p.id,
            p.name,
            p.slug,
            p.price,
            p.sale_price,
            pi.image_path as image
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE p.status = 'active' 
        AND (p.name LIKE ? OR p.description LIKE ? OR p.meta_keywords LIKE ?)
        ORDER BY p.sales_count DESC
        LIMIT 10
    ");
    
    $stmt->execute([$search_term, $search_term, $search_term]);
    $products = $stmt->fetchAll();
    
    jsonResponse(['success' => true, 'products' => $products]);
    
} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Search failed'], 500);
}
?>
