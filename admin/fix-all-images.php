<?php
require_once '../config/database.php';

echo "<!DOCTYPE html><html><head><title>Fix Product Images</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}.success{color:green;}.error{color:red;}.container{max-width:800px;margin:0 auto;background:white;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}</style>";
echo "</head><body><div class='container'>";
echo "<h1>🔧 Fix Product Images</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all products
    $stmt = $db->query("SELECT id, name FROM products ORDER BY id");
    $products = $stmt->fetchAll();
    
    if (empty($products)) {
        echo "<p class='error'>❌ No products found in database!</p>";
        echo "<p>Please run <a href='setup-products.php'>setup-products.php</a> first.</p>";
        exit;
    }
    
    echo "<h2>Found " . count($products) . " products</h2>";
    
    // Get all available images
    $images = glob('../assets/images/products/*');
    echo "<p>Found " . count($images) . " images in folder</p><hr>";
    
    $fixed = 0;
    
    foreach ($products as $product) {
        echo "<div style='padding:15px;margin:10px 0;border:1px solid #ddd;border-radius:8px;'>";
        echo "<h3>{$product['name']}</h3>";
        
        // Check if product already has an image
        $stmt = $db->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND is_primary = 1");
        $stmt->execute([$product['id']]);
        $existing = $stmt->fetch();
        
        if ($existing && file_exists('../' . $existing['image_path'])) {
            echo "<p class='success'>✅ Already has image: " . basename($existing['image_path']) . "</p>";
            echo "<img src='../{$existing['image_path']}' style='max-width:150px;border-radius:8px;'>";
        } else {
            // Assign an image
            if (!empty($images)) {
                $image = $images[$fixed % count($images)];
                $image_path = str_replace('../', '', $image);
                
                // Delete old image entry if exists
                $stmt = $db->prepare("DELETE FROM product_images WHERE product_id = ?");
                $stmt->execute([$product['id']]);
                
                // Insert new image
                $stmt = $db->prepare("INSERT INTO product_images (product_id, image_path, is_primary, display_order) VALUES (?, ?, 1, 0)");
                $stmt->execute([$product['id'], $image_path]);
                
                echo "<p class='success'>✅ Assigned image: " . basename($image) . "</p>";
                echo "<img src='$image' style='max-width:150px;border-radius:8px;'>";
                $fixed++;
            }
        }
        
        echo "</div>";
    }
    
    echo "<hr><h2 class='success'>✅ Complete!</h2>";
    echo "<p>Fixed/verified $fixed products</p>";
    echo "<br><a href='../index.php' style='padding:12px 30px;background:#d4af37;color:white;text-decoration:none;border-radius:8px;display:inline-block;font-weight:600;'>View Homepage</a>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>
