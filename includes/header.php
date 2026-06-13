<?php
// Header file - assumes bootstrap.php is already loaded
$current_user = function_exists('getCurrentUser') ? getCurrentUser() : null;
$cart_count = 0;
$wishlist_count = 0;

if (function_exists('isLoggedIn') && isLoggedIn()) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Get cart count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->execute([getUserId()]);
        $cart_count = $stmt->fetch()['count'];
        
        // Get wishlist count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
        $stmt->execute([getUserId()]);
        $wishlist_count = $stmt->fetch()['count'];
    } catch (Exception $e) {
        error_log("Header error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $page_description ?? 'Luxury perfumes and fragrances - Perfume Palace'; ?>">
    <meta name="keywords" content="<?php echo $page_keywords ?? 'perfume, fragrance, luxury, eau de parfum'; ?>">
    <title><?php echo $page_title ?? 'Perfume Palace - Luxury Fragrances'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <!-- Critical CSS for immediate rendering -->
    <style>
        /* Category Cards */
        .row {
            margin-left: -15px;
            margin-right: -15px;
        }
        .row > * {
            padding-left: 15px;
            padding-right: 15px;
        }
        .category-card {
            position: relative;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            width: 100%;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.16);
        }
        .category-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            display: flex;
            align-items: flex-end;
            padding: 30px;
            z-index: 2;
        }
        .category-name {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        /* Product Cards */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
            width: 100%;
        }
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            width: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.16);
        }
        .product-image-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            background: #f5f5f5;
        }
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .product-card:hover .product-actions {
            opacity: 1;
        }
        .action-btn {
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            background: #d4af37;
            color: white;
            transform: scale(1.1);
        }
        .product-badges {
            position: absolute;
            top: 15px;
            left: 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 2;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-new {
            background: #28a745;
            color: white;
        }
        .badge-sale {
            background: #dc3545;
            color: white;
        }
        .badge-featured {
            background: #ffc107;
            color: #000;
        }
        .product-info {
            padding: 20px;
        }
        .product-category {
            color: #999;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .product-name {
            color: #333;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
        }
        .product-price {
            margin: 15px 0;
        }
        .current-price {
            color: #d4af37;
            font-size: 24px;
            font-weight: 700;
        }
        .original-price {
            color: #999;
            font-size: 16px;
            text-decoration: line-through;
            margin-left: 10px;
        }
        .add-to-cart-btn {
            width: 100%;
            padding: 12px;
            background: #d4af37;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .add-to-cart-btn:hover {
            background: #c9a961;
        }
        
        /* Sections */
        .section {
            padding: 80px 0;
        }
        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #2c2c2c;
        }
        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 16px;
            margin-bottom: 50px;
        }
        
        /* Hero Section */
        .hero-section {
            position: relative;
            height: 600px;
            overflow: hidden;
        }
        .hero-slide {
            height: 600px;
            display: flex;
            align-items: center;
            position: relative;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 600px;
        }
        .hero-subtitle {
            font-size: 18px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .hero-title {
            font-size: 56px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .hero-description {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        /* Features */
        .features-section {
            background: #f8f9fa;
        }
        .feature-box {
            text-align: center;
            padding: 30px 20px;
        }
        .feature-icon {
            width: 80px;
            height: 80px;
            background: #d4af37;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }
        .feature-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .feature-text {
            color: #666;
            font-size: 14px;
        }
        
        /* Loading Spinner */
        #loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        #loading-spinner.show {
            display: flex;
        }
    </style>
    
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-spa brand-icon"></i>
                <span class="brand-text"><?php echo SITE_NAME; ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            Shop
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="products.php">All Products</a></li>
                            <li><a class="dropdown-item" href="products.php?category=men-perfumes">Men's Perfumes</a></li>
                            <li><a class="dropdown-item" href="products.php?category=women-perfumes">Women's Perfumes</a></li>
                            <li><a class="dropdown-item" href="products.php?category=unisex-perfumes">Unisex</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="products.php?filter=featured">Featured</a></li>
                            <li><a class="dropdown-item" href="products.php?filter=new">New Arrivals</a></li>
                            <li><a class="dropdown-item" href="products.php?filter=bestseller">Best Sellers</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php?filter=sale">Sale</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                
                <div class="navbar-actions">
                    <!-- Search -->
                    <div class="search-box">
                        <input type="text" id="search-input" placeholder="Search products..." autocomplete="off">
                        <i class="fas fa-search"></i>
                        <div id="search-results" class="search-results"></div>
                    </div>
                    
                    <!-- Wishlist -->
                    <a href="wishlist.php" class="nav-icon">
                        <i class="far fa-heart"></i>
                        <?php if ($wishlist_count > 0): ?>
                            <span class="badge"><?php echo $wishlist_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Cart -->
                    <a href="cart.php" class="nav-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge" id="cart-count-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- User Account -->
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown">
                            <a href="#" class="nav-icon dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="far fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box"></i> My Orders</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/login.php" style="color: #667eea; font-weight: 600;"><i class="fas fa-shield-alt"></i> Admin Panel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-sm">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
