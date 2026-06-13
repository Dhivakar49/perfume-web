<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Home - Perfume Palace | Luxury Fragrances';
$page_description = 'Discover premium luxury perfumes and fragrances at Perfume Palace. Shop authentic designer perfumes for men and women.';
$page_keywords = 'perfume, fragrance, luxury perfume, eau de parfum, designer perfume';

include 'includes/header.php';

// Fetch featured products
try {
    $db = Database::getInstance()->getConnection();
    
    // Get hero banners
    $stmt = $db->prepare("
        SELECT * FROM banners 
        WHERE position = 'hero' AND is_active = 1 
        AND (start_date IS NULL OR start_date <= NOW())
        AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY display_order ASC
        LIMIT 3
    ");
    $stmt->execute();
    $banners = $stmt->fetchAll();
    
    // Get featured products
    $stmt = $db->prepare("
        SELECT p.*, pi.image_path, c.name as category_name, b.name as brand_name
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_featured = 1 AND p.status = 'active'
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $stmt->execute();
    $featured_products = $stmt->fetchAll();
    
    // Get new arrivals
    $stmt = $db->prepare("
        SELECT p.*, pi.image_path, c.name as category_name, b.name as brand_name
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_new = 1 AND p.status = 'active'
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $stmt->execute();
    $new_arrivals = $stmt->fetchAll();
    
    // Get bestsellers
    $stmt = $db->prepare("
        SELECT p.*, pi.image_path, c.name as category_name, b.name as brand_name
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_bestseller = 1 AND p.status = 'active'
        ORDER BY p.sales_count DESC
        LIMIT 8
    ");
    $stmt->execute();
    $bestsellers = $stmt->fetchAll();
    
    // Get categories
    $stmt = $db->prepare("
        SELECT * FROM categories 
        WHERE is_active = 1 AND parent_id IS NULL
        ORDER BY display_order ASC
        LIMIT 6
    ");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Homepage error: " . $e->getMessage());
}
?>

<!-- Hero Slider -->
<section class="hero-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Hero Slide 1 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content" data-aos="fade-up">
                            <p class="hero-subtitle">Welcome to Luxury</p>
                            <h1 class="hero-title">Discover Your Signature Scent</h1>
                            <p class="hero-description">Explore our exclusive collection of premium fragrances</p>
                            <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Slide 2 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content" data-aos="fade-up">
                            <p class="hero-subtitle">New Arrivals</p>
                            <h1 class="hero-title">Latest Perfume Collection</h1>
                            <p class="hero-description">Discover the newest additions to our luxury range</p>
                            <a href="products.php?filter=new" class="btn btn-primary btn-lg">Explore New</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Slide 3 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content" data-aos="fade-up">
                            <p class="hero-subtitle">Special Offer</p>
                            <h1 class="hero-title">Up to 30% Off</h1>
                            <p class="hero-description">Limited time offer on selected premium perfumes</p>
                            <a href="products.php?filter=sale" class="btn btn-primary btn-lg">Shop Sale</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>



<!-- Featured Products -->
<?php if (!empty($featured_products)): ?>
<section class="section bg-light">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Featured Products</h2>
        <p class="section-subtitle" data-aos="fade-up">Handpicked favorites just for you</p>
        
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="products.php?filter=featured" class="btn btn-outline-primary btn-lg">View All Featured</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="features-section section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h4 class="feature-title">Free Shipping</h4>
                    <p class="feature-text">On orders above ₹<?php echo FREE_SHIPPING_THRESHOLD; ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="feature-title">100% Authentic</h4>
                    <p class="feature-text">Genuine products guaranteed</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h4 class="feature-title">Easy Returns</h4>
                    <p class="feature-text">7-day return policy</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="feature-title">24/7 Support</h4>
                    <p class="feature-text">Dedicated customer service</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<?php if (!empty($new_arrivals)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">New Arrivals</h2>
        <p class="section-subtitle" data-aos="fade-up">Discover the latest additions to our collection</p>
        
        <div class="products-grid">
            <?php foreach ($new_arrivals as $product): ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="products.php?filter=new" class="btn btn-outline-primary btn-lg">View All New Arrivals</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Bestsellers -->
<?php if (!empty($bestsellers)): ?>
<section class="section bg-light">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Best Sellers</h2>
        <p class="section-subtitle" data-aos="fade-up">Most loved by our customers</p>
        
        <div class="products-grid">
            <?php foreach ($bestsellers as $product): ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="products.php?filter=bestseller" class="btn btn-outline-primary btn-lg">View All Bestsellers</a>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Initialize Hero Swiper
const heroSwiper = new Swiper('.hero-swiper', {
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    }
});
</script>

<?php include 'includes/footer.php'; ?>
