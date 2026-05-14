<?php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Product Images</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; }
        h1 { color: #333; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .image-item { text-align: center; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px; }
        .image-item img { max-width: 100%; height: 150px; object-fit: cover; border-radius: 8px; }
        .image-item p { margin: 10px 0 0 0; font-size: 12px; color: #666; word-break: break-all; }
        .product-list { margin-top: 20px; }
        .product-item { padding: 15px; margin: 10px 0; background: white; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; gap: 15px; }
        .product-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .product-info { flex: 1; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .btn { padding: 10px 20px; background: #d4af37; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #c9a961; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Manage Product Images</h1>
        
        <div class="section">
            <h2>📁 Images in Folder (assets/images/products/)</h2>
            <?php
            $images = glob('assets/images/products/*');
            if (empty($images)) {
                echo "<p class='warning'>⚠️ No images found in folder!</p>";
                echo "<p>Please add images to: <code>assets/images/products/</code></p>";
            } else {
                echo "<p class='success'>✅ Found " . count($images) . " images</p>";
                echo "<div class='image-grid'>";
                foreach ($images as $img) {
                    $filename = basename($img);
                    echo "<div class='image-item'>";
                    echo "<img src='$img' alt='$filename'>";
                    echo "<p>$filename</p>";
                    echo "</div>";
                }
                echo "</div>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>📦 Products in Database</h2>
            <?php
            try {
                $db = Database::getInstance()->getConnection();
                
                $stmt = $db->query("
                    SELECT p.id, p.name, pi.image_path
                    FROM products p
                    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                    ORDER BY p.id
                ");
                $products = $stmt->fetchAll();
                
                if (empty($products)) {
                    echo "<p class='warning'>⚠️ No products in database!</p>";
                    echo "<p><a href='setup-products.php' class='btn'>Add Products</a></p>";
                } else {
                    echo "<p class='success'>✅ Found " . count($products) . " products</p>";
                    echo "<div class='product-list'>";
                    
                    foreach ($products as $product) {
                        echo "<div class='product-item'>";
                        
                        if ($product['image_path'] && file_exists($product['image_path'])) {
                            echo "<img src='{$product['image_path']}' alt='{$product['name']}'>";
                            echo "<div class='product-info'>";
                            echo "<strong>{$product['name']}</strong>";
                            echo "<p class='success'>✅ Image: " . basename($product['image_path']) . "</p>";
                            echo "</div>";
                        } else {
                            echo "<div style='width: 80px; height: 80px; background: #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;'>❌</div>";
                            echo "<div class='product-info'>";
                            echo "<strong>{$product['name']}</strong>";
                            echo "<p class='error'>❌ No image assigned</p>";
                            echo "</div>";
                        }
                        
                        echo "</div>";
                    }
                    
                    echo "</div>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>🔧 Quick Actions</h2>
            <p>
                <a href="fix-missing-images.php" class="btn">Auto-Fix Missing Images</a>
                <a href="index.php" class="btn" style="background: #6c757d;">View Homepage</a>
            </p>
        </div>
        
        <div class="section">
            <h2>📝 Instructions</h2>
            <ol>
                <li>Add your perfume images to: <code>assets/images/products/</code></li>
                <li>Refresh this page to see the new images</li>
                <li>Click "Auto-Fix Missing Images" to assign images to products</li>
                <li>Or manually update the database if you know SQL</li>
            </ol>
        </div>
    </div>
</body>
</html>
