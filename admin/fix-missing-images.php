<?php
require_once 'config/database.php';

echo "<h1>Fix Missing Product Images</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; } .warning { color: orange; }</style>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all products with their images
    $stmt = $db->query("
        SELECT p.id, p.name, pi.image_path
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    ");
    $products = $stmt->fetchAll();
    
    echo "<h2>Checking " . count($products) . " products...</h2>";
    
    $fixed = 0;
    $missing = 0;
    
    foreach ($products as $product) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
        echo "<h3>{$product['name']}</h3>";
        
        if (!$product['image_path']) {
            echo "<p class='warning'>⚠️ No image assigned in database</p>";
            $missing++;
        } elseif (!file_exists($product['image_path'])) {
            echo "<p class='error'>❌ Image file not found: {$product['image_path']}</p>";
            
            // Try to find any available image
            $available_images = glob('assets/images/products/*');
            if (!empty($available_images)) {
                $random_image = $available_images[array_rand($available_images)];
                
                // Update to use an existing image
                $stmt = $db->prepare("
                    INSERT INTO product_images (product_id, image_path, is_primary, display_order)
                    VALUES (?, ?, 1, 0)
                    ON DUPLICATE KEY UPDATE image_path = ?
                ");
                $stmt->execute([$product['id'], $random_image, $random_image]);
                
                echo "<p class='success'>✅ Assigned placeholder image: $random_image</p>";
                $fixed++;
            }
        } else {
            echo "<p class='success'>✅ Image OK: {$product['image_path']}</p>";
            echo "<img src='{$product['image_path']}' style='max-width: 150px; border: 1px solid #ddd; border-radius: 8px;'>";
        }
        
        echo "</div>";
    }
    
    echo "<br><h2>Summary:</h2>";
    echo "<p class='success'>✅ Fixed: $fixed products</p>";
    echo "<p class='warning'>⚠️ Missing: $missing products</p>";
    echo "<br><a href='index.php' style='padding: 12px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600;'>View Homepage</a>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
