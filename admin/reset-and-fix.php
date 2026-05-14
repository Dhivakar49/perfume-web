<?php
require_once '../config/database.php';

echo "<!DOCTYPE html><html><head><title>Reset & Fix Images</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}.success{color:green;}.error{color:red;}.warning{color:orange;}.container{max-width:900px;margin:0 auto;background:white;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}img{max-width:150px;border:1px solid #ddd;border-radius:8px;margin:10px 0;}</style>";
echo "</head><body><div class='container'>";
echo "<h1>🔧 Reset & Fix All Product Images</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Step 1: Clear all existing product images
    echo "<h2>Step 1: Clearing old image entries...</h2>";
    $stmt = $db->query("DELETE FROM product_images");
    echo "<p class='success'>✅ Cleared all old image entries</p>";
    
    // Step 2: Get all products
    echo "<h2>Step 2: Getting products...</h2>";
    $stmt = $db->query("SELECT id, name FROM products ORDER BY id");
    $products = $stmt->fetchAll();
    echo "<p class='success'>✅ Found " . count($products) . " products</p>";
    
    // Step 3: Get all images
    echo "<h2>Step 3: Finding images...</h2>";
    $image_files = [
        'Opal Mist.webp',
        'Velvet Petal.webp',
        'Pearl Essence.png',
        'Eternity Luxe.jpeg',
        'Luxe Aurora.jpg',
        'Citrus-Breeze.jpg',
        'Leather-Oud.jpg',
        'Midnight-Bloom.jpg',
        'Mystic-Woods.jpg',
        'Noir-Intense.jpg',
        'Ocean-Blue.jpg',
        'Rose-Garden.jpg'
    ];
    
    $available_images = [];
    foreach ($image_files as $img) {
        $path = '../assets/images/products/' . $img;
        if (file_exists($path)) {
            $available_images[] = 'assets/images/products/' . $img;
        }
    }
    
    echo "<p class='success'>✅ Found " . count($available_images) . " valid images</p>";
    
    // Step 4: Assign images to products
    echo "<h2>Step 4: Assigning images to products...</h2>";
    
    $image_index = 0;
    foreach ($products as $product) {
        $image_path = $available_images[$image_index % count($available_images)];
        
        // Insert image
        $stmt = $db->prepare("INSERT INTO product_images (product_id, image_path, is_primary, display_order) VALUES (?, ?, 1, 0)");
        $stmt->execute([$product['id'], $image_path]);
        
        echo "<div style='padding:15px;margin:10px 0;border:1px solid #ddd;border-radius:8px;background:#f9f9f9;'>";
        echo "<strong>{$product['name']}</strong><br>";
        echo "<span class='success'>✅ Assigned: " . basename($image_path) . "</span><br>";
        echo "<img src='../$image_path' alt='{$product['name']}'>";
        echo "</div>";
        
        $image_index++;
    }
    
    echo "<hr>";
    echo "<h2 class='success'>✅ All Done!</h2>";
    echo "<p>Successfully assigned images to " . count($products) . " products.</p>";
    echo "<br><a href='../index.php' style='padding:15px 40px;background:#d4af37;color:white;text-decoration:none;border-radius:8px;display:inline-block;font-weight:600;font-size:16px;'>View Homepage</a>";
    echo " <a href='reset-and-fix.php' style='padding:15px 40px;background:#6c757d;color:white;text-decoration:none;border-radius:8px;display:inline-block;font-weight:600;font-size:16px;margin-left:10px;'>Run Again</a>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div></body></html>";
?>
