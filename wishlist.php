<?php
require_once __DIR__ . '/bootstrap.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Wishlist - Perfume Palace';
$user_id = getUserId();

// Fetch wishlist items
try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("
        SELECT p.*, w.created_at as added_at,
               pi.image_path
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $wishlist_items = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Wishlist page error: " . $e->getMessage());
    $wishlist_items = [];
}

include 'includes/header.php';
?>

<div class="container" style="padding: 60px 15px;">
    <h1 style="margin-bottom: 30px; font-size: 32px; font-weight: 700;">My Wishlist</h1>
    
    <?php if (empty($wishlist_items)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px;">
            <i class="far fa-heart" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">Your Wishlist is Empty</h3>
            <p style="color: #999; margin-bottom: 30px;">Save your favorite items here for later.</p>
            <a href="products.php" class="btn btn-primary" style="padding: 12px 30px; background: #d4af37; color: white; border: none; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600;">
                Browse Products
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="wishlist-item" data-product-id="<?php echo $item['id']; ?>" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
                    <div style="position: relative; height: 300px; background: #f5f5f5;">
                        <?php if ($item['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 48px;">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <button onclick="removeFromWishlist(<?php echo $item['id']; ?>)" 
                                style="position: absolute; top: 15px; right: 15px; width: 40px; height: 40px; background: white; border: none; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); color: #dc3545;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div style="padding: 20px;">
                        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">
                            <a href="product-details.php?id=<?php echo $item['id']; ?>" style="color: #333; text-decoration: none;">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </a>
                        </h3>
                        
                        <div style="margin-bottom: 15px;">
                            <?php 
                            $price = $item['sale_price'] ?? $item['price'];
                            $has_discount = isset($item['sale_price']) && $item['sale_price'] < $item['price'];
                            ?>
                            <span style="color: #d4af37; font-size: 24px; font-weight: 700;">
                                ₹<?php echo number_format($price, 2); ?>
                            </span>
                            <?php if ($has_discount): ?>
                                <span style="color: #999; font-size: 16px; text-decoration: line-through; margin-left: 10px;">
                                    ₹<?php echo number_format($item['price'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <p style="color: #999; font-size: 13px; margin-bottom: 15px;">
                            Added <?php echo date('M j, Y', strtotime($item['added_at'])); ?>
                        </p>
                        
                        <?php if ($item['stock'] > 0): ?>
                            <button onclick="addToCart(<?php echo $item['id']; ?>)" 
                                    style="width: 100%; padding: 12px; background: #d4af37; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <button disabled style="width: 100%; padding: 12px; background: #ccc; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: not-allowed;">
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
