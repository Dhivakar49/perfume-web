<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if products already exist
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $count = $stmt->fetch()['count'];
    
    if ($count > 0) {
        echo "Products already exist! Count: $count<br>";
        echo '<a href="index.php">Go to Homepage</a>';
        exit;
    }
    
    // Product data with images
    $products = [
        [
            'name' => 'Eternity Luxe',
            'description' => 'A timeless fragrance with notes of jasmine, sandalwood, and vanilla. Perfect for evening wear and special occasions.',
            'short_description' => 'Timeless elegance with jasmine and sandalwood',
            'price' => 4999.00,
            'sale_price' => 3999.00,
            'image' => 'assets/images/products/Eternity Luxe.jpeg',
            'stock' => 50,
            'is_featured' => 1,
            'is_new' => 1,
            'is_bestseller' => 1
        ],
        [
            'name' => 'Luxe Aurora',
            'description' => 'Elegant and sophisticated with hints of rose, amber, and musk. A luxurious scent for the modern woman.',
            'short_description' => 'Elegant rose and amber blend',
            'price' => 5499.00,
            'sale_price' => null,
            'image' => 'assets/images/products/Luxe Aurora.jpg',
            'stock' => 45,
            'is_featured' => 1,
            'is_new' => 0,
            'is_bestseller' => 1
        ],
        [
            'name' => 'Opal Mist',
            'description' => 'Fresh and invigorating with citrus, mint, and ocean notes. Perfect for daily wear and active lifestyles.',
            'short_description' => 'Fresh citrus and mint fusion',
            'price' => 3999.00,
            'sale_price' => 2999.00,
            'image' => 'assets/images/products/Opal Mist.webp',
            'stock' => 60,
            'is_featured' => 1,
            'is_new' => 1,
            'is_bestseller' => 0
        ],
        [
            'name' => 'Pearl Essence',
            'description' => 'Delicate floral fragrance with peony, lily, and white tea. Light and refreshing for everyday elegance.',
            'short_description' => 'Delicate floral with peony and lily',
            'price' => 4499.00,
            'sale_price' => null,
            'image' => 'assets/images/products/Pearl Essence.png',
            'stock' => 40,
            'is_featured' => 0,
            'is_new' => 1,
            'is_bestseller' => 0
        ],
        [
            'name' => 'Velvet Petal',
            'description' => 'Rich and luxurious with vanilla, musk, and patchouli. A warm, sensual fragrance for confident individuals.',
            'short_description' => 'Rich vanilla and musk blend',
            'price' => 5999.00,
            'sale_price' => 4999.00,
            'image' => 'assets/images/products/Velvet Petal.webp',
            'stock' => 35,
            'is_featured' => 1,
            'is_new' => 0,
            'is_bestseller' => 1
        ]
    ];
    
    $added = 0;
    foreach ($products as $product) {
        // Check if image exists
        if (!file_exists($product['image'])) {
            echo "⚠️ Image not found: {$product['image']}<br>";
            continue;
        }
        
        // Insert product
        $stmt = $db->prepare("
            INSERT INTO products (
                name, description, short_description, price, sale_price, 
                image_path, stock, status, is_featured, is_new, is_bestseller,
                created_at, updated_at
            ) VALUES (
                :name, :description, :short_description, :price, :sale_price,
                :image_path, :stock, 'active', :is_featured, :is_new, :is_bestseller,
                NOW(), NOW()
            )
        ");
        
        $stmt->execute([
            'name' => $product['name'],
            'description' => $product['description'],
            'short_description' => $product['short_description'],
            'price' => $product['price'],
            'sale_price' => $product['sale_price'],
            'image_path' => $product['image'],
            'stock' => $product['stock'],
            'is_featured' => $product['is_featured'],
            'is_new' => $product['is_new'],
            'is_bestseller' => $product['is_bestseller']
        ]);
        
        $added++;
        echo "✅ Added: {$product['name']}<br>";
    }
    
    echo "<br><strong>Success! Added $added products.</strong><br><br>";
    echo '<a href="index.php" style="padding: 12px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600;">View Homepage</a>';
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
