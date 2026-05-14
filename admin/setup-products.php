<?php
require_once 'config/database.php';

echo "<h1>Product Setup</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; }</style>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if products exist
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $count = $stmt->fetch()['count'];
    
    if ($count > 0) {
        echo "<p class='error'>Products already exist! ($count products found)</p>";
        echo "<p>If you want to reset, delete products first.</p>";
        echo '<a href="index.php">Go to Homepage</a>';
        exit;
    }
    
    // First, create a default category
    $stmt = $db->prepare("INSERT INTO categories (name, slug, description, is_active) VALUES (?, ?, ?, 1)");
    $stmt->execute(['Luxury Perfumes', 'luxury-perfumes', 'Premium luxury fragrances']);
    $category_id = $db->lastInsertId();
    echo "<p class='success'>✅ Created category: Luxury Perfumes</p>";
    
    // Product data
    $products = [
        [
            'name' => 'Eternity Luxe',
            'description' => 'A timeless fragrance with notes of jasmine, sandalwood, and vanilla. Perfect for evening wear and special occasions. This luxurious scent combines floral elegance with warm woody undertones.',
            'short_description' => 'Timeless elegance with jasmine and sandalwood',
            'price' => 4999.00,
            'sale_price' => 3999.00,
            'image' => 'Eternity Luxe.jpeg',
            'stock' => 50
        ],
        [
            'name' => 'Luxe Aurora',
            'description' => 'Elegant and sophisticated with hints of rose, amber, and musk. A luxurious scent for the modern woman who appreciates refined fragrances.',
            'short_description' => 'Elegant rose and amber blend',
            'price' => 5499.00,
            'sale_price' => null,
            'image' => 'Luxe Aurora.jpg',
            'stock' => 45
        ],
        [
            'name' => 'Opal Mist',
            'description' => 'Fresh and invigorating with citrus, mint, and ocean notes. Perfect for daily wear and active lifestyles. A refreshing scent that energizes your day.',
            'short_description' => 'Fresh citrus and mint fusion',
            'price' => 3999.00,
            'sale_price' => 2999.00,
            'image' => 'Opal Mist.webp',
            'stock' => 60
        ],
        [
            'name' => 'Pearl Essence',
            'description' => 'Delicate floral fragrance with peony, lily, and white tea. Light and refreshing for everyday elegance. A gentle scent perfect for any occasion.',
            'short_description' => 'Delicate floral with peony and lily',
            'price' => 4499.00,
            'sale_price' => null,
            'image' => 'Pearl Essence.png',
            'stock' => 40
        ],
        [
            'name' => 'Velvet Petal',
            'description' => 'Rich and luxurious with vanilla, musk, and patchouli. A warm, sensual fragrance for confident individuals who love bold scents.',
            'short_description' => 'Rich vanilla and musk blend',
            'price' => 5999.00,
            'sale_price' => 4999.00,
            'image' => 'Velvet Petal.webp',
            'stock' => 35
        ]
    ];
    
    $added = 0;
    foreach ($products as $index => $product) {
        // Generate slug and SKU
        $slug = strtolower(str_replace(' ', '-', $product['name']));
        $sku = 'PERF-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
        
        // Insert product
        $stmt = $db->prepare("
            INSERT INTO products (
                name, slug, sku, description, short_description, 
                category_id, price, sale_price, stock, 
                is_featured, is_new, is_bestseller, status,
                created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, 1, 'active', NOW(), NOW()
            )
        ");
        
        $stmt->execute([
            $product['name'],
            $slug,
            $sku,
            $product['description'],
            $product['short_description'],
            $category_id,
            $product['price'],
            $product['sale_price'],
            $product['stock']
        ]);
        
        $product_id = $db->lastInsertId();
        
        // Insert product image
        $image_path = 'assets/images/products/' . $product['image'];
        if (file_exists($image_path)) {
            $stmt = $db->prepare("
                INSERT INTO product_images (product_id, image_path, is_primary, display_order)
                VALUES (?, ?, 1, 0)
            ");
            $stmt->execute([$product_id, $image_path]);
            
            echo "<p class='success'>✅ Added: {$product['name']} (with image)</p>";
        } else {
            echo "<p class='error'>⚠️ Added: {$product['name']} (image not found: $image_path)</p>";
        }
        
        $added++;
    }
    
    echo "<br><h2 class='success'>Success! Added $added products.</h2>";
    echo "<p>All products have been added to the database with images.</p>";
    echo '<br><a href="index.php" style="padding: 12px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600;">View Homepage</a>';
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
