<?php
// Product Card Component
// Usage: Include this file in a loop with $product variable set

$image = $product['image_path'] ?? $product['image'] ?? 'default-product.jpg';
$price = $product['sale_price'] ?? $product['price'];
$has_discount = isset($product['sale_price']) && $product['sale_price'] < $product['price'];
$discount_percent = $has_discount ? calculateDiscountPercentage($product['price'], $product['sale_price']) : 0;
$rating = $product['rating_avg'] ?? 0;
$rating_count = $product['rating_count'] ?? 0;
?>

<div class="product-card" data-aos="fade-up">
    <div class="product-image-wrapper">
        <img src="<?php echo htmlspecialchars($image); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" 
             class="product-image">
        
        <!-- Badges -->
        <div class="product-badges">
            <?php if ($product['is_new'] ?? false): ?>
                <span class="badge badge-new">New</span>
            <?php endif; ?>
            <?php if ($has_discount): ?>
                <span class="badge badge-sale"><?php echo $discount_percent; ?>% OFF</span>
            <?php endif; ?>
            <?php if ($product['is_featured'] ?? false): ?>
                <span class="badge badge-featured">Featured</span>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="product-actions">
            <button class="action-btn wishlist-btn" 
                    data-product-id="<?php echo $product['id']; ?>"
                    onclick="addToWishlist(<?php echo $product['id']; ?>)"
                    title="Add to Wishlist">
                <i class="far fa-heart"></i>
            </button>
            <button class="action-btn quick-view-btn" 
                    onclick="showQuickView(<?php echo $product['id']; ?>)"
                    title="Quick View">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="product-info">
        <p class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Perfume'); ?></p>
        
        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="product-name">
            <?php echo htmlspecialchars($product['name']); ?>
        </a>
        
        <?php if ($rating > 0): ?>
            <div class="product-rating">
                <span class="stars">
                    <?php
                    $full_stars = floor($rating);
                    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
                    $empty_stars = 5 - $full_stars - $half_star;
                    
                    for ($i = 0; $i < $full_stars; $i++) {
                        echo '<i class="fas fa-star"></i>';
                    }
                    if ($half_star) {
                        echo '<i class="fas fa-star-half-alt"></i>';
                    }
                    for ($i = 0; $i < $empty_stars; $i++) {
                        echo '<i class="far fa-star"></i>';
                    }
                    ?>
                </span>
                <span class="rating-count">(<?php echo $rating_count; ?>)</span>
            </div>
        <?php endif; ?>
        
        <div class="product-price">
            <span class="current-price"><?php echo formatPrice($price); ?></span>
            <?php if ($has_discount): ?>
                <span class="original-price"><?php echo formatPrice($product['price']); ?></span>
                <span class="discount-percent">-<?php echo $discount_percent; ?>%</span>
            <?php endif; ?>
        </div>
        
        <?php if (($product['stock'] ?? 0) > 0): ?>
            <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['id']; ?>)">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
        <?php else: ?>
            <button class="add-to-cart-btn" disabled style="background: #ccc; cursor: not-allowed;">
                Out of Stock
            </button>
        <?php endif; ?>
    </div>
</div>
