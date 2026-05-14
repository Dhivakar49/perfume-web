<?php
require_once 'config/database.php';

echo "<h1>Image Path Checker</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; } img { max-width: 200px; border: 1px solid #ddd; margin: 10px 0; }</style>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all products
    $stmt = $db->query("SELECT id, name, image_path FROM products");
    $products = $stmt->fetchAll();
    
    echo "<h2>Products in Database: " . count($products) . "</h2>";
    
    foreach ($products as $product) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
        echo "<h3>{$product['name']}</h3>";
        echo "<p><strong>Database Path:</strong> <code>{$product['image_path']}</code></p>";
        
        // Check if file exists
        if (file_exists($product['image_path'])) {
            echo "<p class='success'>✅ File exists!</p>";
            echo "<img src='{$product['image_path']}' alt='{$product['name']}'>";
        } else {
            echo "<p class='error'>❌ File NOT found at this path</p>";
            
            // Try to find the file
            $filename = basename($product['image_path']);
            $possible_paths = [
                "assets/images/products/$filename",
                "assets/images/products/" . str_replace(' ', '%20', $filename),
                "assets/images/products/" . urlencode($filename)
            ];
            
            echo "<p><strong>Trying alternative paths:</strong></p>";
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    echo "<p class='success'>✅ Found at: <code>$path</code></p>";
                    echo "<img src='$path' alt='{$product['name']}'>";
                    
                    // Update database
                    $update = $db->prepare("UPDATE products SET image_path = ? WHERE id = ?");
                    $update->execute([$path, $product['id']]);
                    echo "<p class='success'>✅ Database updated!</p>";
                    break;
                } else {
                    echo "<p class='error'>❌ Not at: <code>$path</code></p>";
                }
            }
        }
        echo "</div>";
    }
    
    echo "<br><br>";
    echo "<h2>Available Images in Folder:</h2>";
    $images = glob("assets/images/products/*");
    foreach ($images as $img) {
        echo "<div style='display: inline-block; margin: 10px; text-align: center;'>";
        echo "<img src='$img' style='max-width: 150px;'><br>";
        echo "<small>" . basename($img) . "</small>";
        echo "</div>";
    }
    
    echo "<br><br><a href='index.php' style='padding: 12px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 8px; display: inline-block;'>Go to Homepage</a>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>
