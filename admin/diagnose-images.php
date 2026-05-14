<?php
require_once '../config/database.php';

echo "<!DOCTYPE html><html><head><title>Image Diagnosis</title>";
echo "<style>
body{font-family:Arial;padding:20px;background:#f5f5f5;}
.container{max-width:1200px;margin:0 auto;background:white;padding:30px;border-radius:12px;}
.success{color:green;} .error{color:red;} .warning{color:orange;}
table{width:100%;border-collapse:collapse;margin:20px 0;}
th,td{padding:12px;text-align:left;border:1px solid #ddd;}
th{background:#f8f9fa;font-weight:600;}
img{max-width:100px;border:1px solid #ddd;border-radius:4px;}
.code{background:#f4f4f4;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:12px;}
</style>";
echo "</head><body><div class='container'>";
echo "<h1>🔍 Image Path Diagnosis</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get products with images
    $stmt = $db->query("
        SELECT p.id, p.name, pi.image_path
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        ORDER BY p.id
    ");
    $products = $stmt->fetchAll();
    
    echo "<h2>Products in Database: " . count($products) . "</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Product Name</th><th>Image Path in DB</th><th>File Exists?</th><th>Preview</th></tr>";
    
    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>{$product['id']}</td>";
        echo "<td><strong>{$product['name']}</strong></td>";
        
        if ($product['image_path']) {
            echo "<td><span class='code'>{$product['image_path']}</span></td>";
            
            // Check if file exists
            $file_path = '../' . $product['image_path'];
            if (file_exists($file_path)) {
                echo "<td class='success'>✅ YES</td>";
                echo "<td><img src='../{$product['image_path']}' alt='{$product['name']}'></td>";
            } else {
                echo "<td class='error'>❌ NO</td>";
                echo "<td class='error'>File not found at: <br><span class='code'>$file_path</span></td>";
            }
        } else {
            echo "<td class='warning'>No image path</td>";
            echo "<td class='warning'>-</td>";
            echo "<td class='warning'>No image assigned</td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show available images
    echo "<hr><h2>Available Images in Folder</h2>";
    $images = glob('../assets/images/products/*');
    echo "<p>Found " . count($images) . " files</p>";
    echo "<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:15px;'>";
    foreach ($images as $img) {
        $relative_path = str_replace('../', '', $img);
        echo "<div style='text-align:center;padding:10px;border:1px solid #ddd;border-radius:8px;'>";
        echo "<img src='$img' style='max-width:100%;height:120px;object-fit:cover;border-radius:4px;'><br>";
        echo "<small style='font-size:11px;'>" . basename($img) . "</small><br>";
        echo "<small class='code' style='font-size:10px;'>$relative_path</small>";
        echo "</div>";
    }
    echo "</div>";
    
    echo "<hr>";
    echo "<h2>🔧 Actions</h2>";
    echo "<a href='reset-and-fix.php' style='padding:12px 30px;background:#d4af37;color:white;text-decoration:none;border-radius:8px;display:inline-block;font-weight:600;margin:5px;'>Fix All Images</a>";
    echo "<a href='../index.php' style='padding:12px 30px;background:#6c757d;color:white;text-decoration:none;border-radius:8px;display:inline-block;font-weight:600;margin:5px;'>View Homepage</a>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>
