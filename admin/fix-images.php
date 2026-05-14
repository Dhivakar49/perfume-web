<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Images - Perfume Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .status {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 10px 10px 0;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover { background: #5568d3; }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover { background: #5a6268; }
        .product-list {
            margin-top: 20px;
        }
        .product-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .product-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .product-path {
            font-size: 12px;
            color: #666;
            font-family: monospace;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Fix Product Images</h1>
        <p class="subtitle">Check and fix product image paths</p>

        <?php
        require_once 'config/database.php';

        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if products table exists
            $stmt = $db->query("SHOW TABLES LIKE 'products'");
            if ($stmt->rowCount() == 0) {
                echo '<div class="status error">❌ Products table does not exist. Please run install.php first.</div>';
                echo '<a href="install.php" class="btn">Run Installation</a>';
                exit;
            }
            
            // Check existing products
            $stmt = $db->query("SELECT COUNT(*) as count FROM products");
            $count = $stmt->fetch()['count'];
            
            if ($count == 0) {
                echo '<div class="status warning">⚠️ No products found in database.</div>';
                
                // Check if we have product images in the folder
                $image_dir = 'assets/images/products/';
                $images = glob($image_dir . '*');
                
                if (empty($images)) {
                    echo '<div class="status info">ℹ️ No product images found in <code>assets/images/products/</code></div>';
                    echo '<p>Please add product images to the folder first.</p>';
                } else {
                    echo '<div class="status success">✅ Found ' . count($images) . ' images in products folder</div>';
                    
                    // Add sample products
                    if (isset($_POST['add_products'])) {
                        $sample_products = [
                            [
                                'name' => 'Eternity Luxe',
                                'description' => 'A timeless fragrance with notes of jasmine and sandalwood',
                                'price' => 4999,
                                'image' => 'Eternity Luxe.jpeg'
                            ],
                            [
                                'name' => 'Luxe Aurora',
                                'description' => 'Elegant and sophisticated with hints of rose and amber',
                                'price' => 5499,
                                'image' => 'Luxe Aurora.jpg'
                            ],
                            [
                                'name' => 'Opal Mist',
                                'description' => 'Fresh and invigorating with citrus and mint notes',
                                'price' => 3999,
                                'image' => 'Opal Mist.webp'
                            ],
                            [
                                'name' => 'Pearl Essence',
                                'description' => 'Delicate floral fragrance with peony and lily',
                                'price' => 4499,
                                'image' => 'Pearl Essence.png'
                            ],
                            [
                                'name' => 'Velvet Petal',
                                'description' => 'Rich and luxurious with vanilla and musk',
                                'price' => 5999,
                                'image' => 'Velvet Petal.webp'
                            ]
                        ];
                        
                        $added = 0;
                        foreach ($sample_products as $product) {
                            $image_path = $image_dir . $product['image'];
                            if (file_exists($image_path)) {
                                $stmt = $db->prepare("
                                    INSERT INTO products (name, description, price, image_path, stock, status, is_featured, is_new, created_at)
                                    VALUES (:name, :description, :price, :image_path, 100, 'active', 1, 1, NOW())
                                ");
                                $stmt->execute([
                                    'name' => $product['name'],
                                    'description' => $product['description'],
                                    'price' => $product['price'],
                                    'image_path' => $image_path
                                ]);
                                $added++;
                            }
                        }
                        
                        echo '<div class="status success">✅ Added ' . $added . ' products successfully!</div>';
                        echo '<a href="index.php" class="btn">View Homepage</a>';
                        echo '<a href="fix-images.php" class="btn btn-secondary">Refresh</a>';
                    } else {
                        echo '<form method="POST">';
                        echo '<button type="submit" name="add_products" class="btn">Add Sample Products</button>';
                        echo '</form>';
                    }
                }
            } else {
                echo '<div class="status success">✅ Found ' . $count . ' products in database</div>';
                
                // Get all products
                $stmt = $db->query("SELECT id, name, image_path, price FROM products LIMIT 20");
                $products = $stmt->fetchAll();
                
                echo '<div class="product-list">';
                echo '<h3>Current Products:</h3>';
                
                $needs_fix = false;
                foreach ($products as $product) {
                    $image_exists = file_exists($product['image_path']);
                    $status_class = $image_exists ? 'success' : 'error';
                    $status_icon = $image_exists ? '✅' : '❌';
                    
                    if (!$image_exists) $needs_fix = true;
                    
                    echo '<div class="product-item">';
                    if ($image_exists) {
                        echo '<img src="' . htmlspecialchars($product['image_path']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                    } else {
                        echo '<div style="width:80px;height:80px;background:#ddd;border-radius:8px;display:flex;align-items:center;justify-content:center;">❌</div>';
                    }
                    echo '<div class="product-info">';
                    echo '<div class="product-name">' . $status_icon . ' ' . htmlspecialchars($product['name']) . '</div>';
                    echo '<div class="product-path">' . htmlspecialchars($product['image_path']) . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                
                if ($needs_fix) {
                    echo '<div class="status warning">⚠️ Some products have missing images</div>';
                    
                    if (isset($_POST['fix_paths'])) {
                        // Fix image paths
                        $fixed = 0;
                        foreach ($products as $product) {
                            if (!file_exists($product['image_path'])) {
                                // Try to find the image in assets/images/products/
                                $filename = basename($product['image_path']);
                                $new_path = 'assets/images/products/' . $filename;
                                
                                if (file_exists($new_path)) {
                                    $stmt = $db->prepare("UPDATE products SET image_path = :new_path WHERE id = :id");
                                    $stmt->execute(['new_path' => $new_path, 'id' => $product['id']]);
                                    $fixed++;
                                }
                            }
                        }
                        
                        echo '<div class="status success">✅ Fixed ' . $fixed . ' image paths!</div>';
                        echo '<a href="fix-images.php" class="btn btn-secondary">Refresh</a>';
                    } else {
                        echo '<form method="POST">';
                        echo '<button type="submit" name="fix_paths" class="btn">Fix Image Paths</button>';
                        echo '</form>';
                    }
                } else {
                    echo '<div class="status success">✅ All product images are working correctly!</div>';
                }
                
                echo '<a href="index.php" class="btn">View Homepage</a>';
            }
            
        } catch (Exception $e) {
            echo '<div class="status error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<p>Make sure MySQL is running and database credentials are correct.</p>';
        }
        ?>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <h3>Quick Links:</h3>
        <a href="index.php" class="btn btn-secondary">Homepage</a>
        <a href="index_debug.php" class="btn btn-secondary">Debug Dashboard</a>
        <a href="install.php" class="btn btn-secondary">Installation</a>
    </div>
</body>
</html>
