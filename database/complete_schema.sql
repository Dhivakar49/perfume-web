-- =====================================================
-- PERFUME PALACE - COMPLETE DATABASE SCHEMA
-- Professional Ecommerce Platform
-- InfinityFree Compatible Version
-- =====================================================
-- 
-- INSTRUCTIONS:
-- 1. Create database in InfinityFree MySQL panel first
-- 2. Import this file using phpMyAdmin
-- 3. This file works with existing databases
-- 
-- =====================================================

-- =====================================================
-- USERS & AUTHENTICATION
-- =====================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(100),
    reset_token VARCHAR(100),
    reset_token_expiry DATETIME,
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- =====================================================
-- PRODUCT MANAGEMENT
-- =====================================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INT DEFAULT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    category_id INT NOT NULL,
    brand_id INT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    cost_price DECIMAL(10, 2),
    stock INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 10,
    weight DECIMAL(8, 2),
    dimensions VARCHAR(50),
    is_featured TINYINT(1) DEFAULT 0,
    is_new TINYINT(1) DEFAULT 0,
    is_bestseller TINYINT(1) DEFAULT 0,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    views INT DEFAULT 0,
    sales_count INT DEFAULT 0,
    rating_avg DECIMAL(3, 2) DEFAULT 0.00,
    rating_count INT DEFAULT 0,
    meta_title VARCHAR(200),
    meta_description VARCHAR(500),
    meta_keywords VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_brand (brand_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price),
    FULLTEXT idx_search (name, description, meta_keywords)
) ENGINE=InnoDB;

CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

CREATE TABLE product_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    attribute_name VARCHAR(50) NOT NULL,
    attribute_value VARCHAR(100) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- =====================================================
-- SHOPPING CART & WISHLIST
-- =====================================================

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- =====================================================
-- ORDERS & PAYMENTS
-- =====================================================

CREATE TABLE shipping_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    country VARCHAR(50) DEFAULT 'India',
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    min_order_amount DECIMAL(10, 2) DEFAULT 0,
    max_discount_amount DECIMAL(10, 2),
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    shipping_address_id INT,
    subtotal DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    coupon_code VARCHAR(50),
    shipping_charge DECIMAL(10, 2) DEFAULT 0,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cod', 'razorpay', 'stripe', 'upi') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_notes TEXT,
    tracking_number VARCHAR(100),
    shipped_at DATETIME,
    delivered_at DATETIME,
    cancelled_at DATETIME,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_address_id) REFERENCES shipping_addresses(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (order_status),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_sku VARCHAR(50),
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transaction_id VARCHAR(100) UNIQUE,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    payment_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_transaction (transaction_id)
) ENGINE=InnoDB;

-- =====================================================
-- REVIEWS & RATINGS
-- =====================================================

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    comment TEXT,
    is_verified_purchase TINYINT(1) DEFAULT 0,
    is_approved TINYINT(1) DEFAULT 0,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_user (user_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB;

-- =====================================================
-- NOTIFICATIONS & ACTIVITY
-- =====================================================

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_admin (admin_id),
    INDEX idx_action (action)
) ENGINE=InnoDB;

-- =====================================================
-- BANNERS & CONTENT
-- =====================================================

CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    button_text VARCHAR(50),
    position ENUM('hero', 'sidebar', 'footer', 'popup') DEFAULT 'hero',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    start_date DATETIME,
    end_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_position (position),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    admin_reply TEXT,
    replied_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- =====================================================
-- RECENTLY VIEWED
-- =====================================================

CREATE TABLE recently_viewed (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- =====================================================
-- SITE SETTINGS
-- =====================================================

CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert Categories
INSERT INTO categories (name, slug, description, is_active) VALUES
('Men Perfumes', 'men-perfumes', 'Exclusive fragrances for men', 1),
('Women Perfumes', 'women-perfumes', 'Elegant fragrances for women', 1),
('Unisex Perfumes', 'unisex-perfumes', 'Versatile fragrances for everyone', 1),
('Luxury Collection', 'luxury-collection', 'Premium luxury perfumes', 1),
('Gift Sets', 'gift-sets', 'Perfect gift sets for loved ones', 1);

-- Insert Brands
INSERT INTO brands (name, slug, description, is_active) VALUES
('Essence Royale', 'essence-royale', 'Premium luxury perfume brand', 1),
('Aroma Elite', 'aroma-elite', 'Contemporary fragrance collection', 1),
('Velvet Scents', 'velvet-scents', 'Soft and elegant perfumes', 1),
('Luxe Parfum', 'luxe-parfum', 'High-end designer fragrances', 1),
('Pearl Fragrances', 'pearl-fragrances', 'Timeless classic scents', 1);

-- Insert Products
INSERT INTO products (name, slug, sku, description, short_description, category_id, brand_id, price, sale_price, stock, is_featured, is_new, is_bestseller, status) VALUES
('Opal Mist Eau De Parfum', 'opal-mist-eau-de-parfum', 'PERF-001', 'A refreshing and elegant fragrance with notes of citrus, jasmine, and sandalwood. Perfect for daily wear and special occasions.', 'Refreshing citrus and floral blend', 2, 1, 2499.00, 1999.00, 50, 1, 1, 1, 'active'),
('Velvet Petal Intense', 'velvet-petal-intense', 'PERF-002', 'Soft floral notes with a touch of luxury. Features rose, peony, and vanilla creating an unforgettable scent experience.', 'Luxurious floral fragrance', 2, 3, 2799.00, 2299.00, 45, 1, 0, 1, 'active'),
('Pearl Essence Classic', 'pearl-essence-classic', 'PERF-003', 'Timeless elegance in a bottle. A sophisticated blend of bergamot, lily, and musk that lasts all day.', 'Timeless elegant scent', 3, 5, 2999.00, NULL, 60, 1, 0, 1, 'active'),
('Eternity Luxe Pour Homme', 'eternity-luxe-pour-homme', 'PERF-004', 'A sophisticated and lasting scent for the modern man. Woody notes combined with spices and amber.', 'Sophisticated masculine fragrance', 1, 4, 3499.00, 2999.00, 40, 1, 1, 0, 'active'),
('Luxe Aurora Night', 'luxe-aurora-night', 'PERF-005', 'Radiant and captivating fragrance for evening wear. Oriental notes with oud, saffron, and leather.', 'Captivating evening scent', 4, 4, 4999.00, 4499.00, 35, 1, 1, 1, 'active'),
('Midnight Noir', 'midnight-noir', 'PERF-006', 'Deep and mysterious fragrance with dark chocolate, coffee, and patchouli notes.', 'Mysterious dark fragrance', 1, 2, 3299.00, NULL, 55, 0, 1, 0, 'active'),
('Rose Garden Delight', 'rose-garden-delight', 'PERF-007', 'Fresh rose petals with hints of green tea and white musk. Perfect for spring and summer.', 'Fresh rose fragrance', 2, 3, 2199.00, 1899.00, 70, 0, 1, 1, 'active'),
('Ocean Breeze', 'ocean-breeze', 'PERF-008', 'Aquatic fresh scent with marine notes, mint, and cedarwood. Ideal for active lifestyle.', 'Fresh aquatic scent', 1, 2, 2399.00, NULL, 65, 0, 0, 1, 'active'),
('Golden Amber', 'golden-amber', 'PERF-009', 'Warm and sensual with amber, vanilla, and tonka bean. A perfect signature scent.', 'Warm amber fragrance', 3, 1, 3799.00, 3299.00, 30, 1, 0, 0, 'active'),
('Citrus Splash', 'citrus-splash', 'PERF-010', 'Energizing citrus blend with lemon, orange, and grapefruit. Fresh and invigorating.', 'Energizing citrus blend', 3, 2, 1999.00, 1699.00, 80, 0, 1, 1, 'active');

-- Insert Product Images (using relative paths compatible with hosting)
INSERT INTO product_images (product_id, image_path, is_primary, display_order) VALUES
(1, 'assets/images/products/Opal Mist.webp', 1, 1),
(2, 'assets/images/products/Velvet Petal.webp', 1, 1),
(3, 'assets/images/products/Pearl Essence.png', 1, 1),
(4, 'assets/images/products/Eternity Luxe.jpeg', 1, 1),
(5, 'assets/images/products/Luxe Aurora.jpg', 1, 1);

-- Insert Admin User (password: admin123)
INSERT INTO admins (username, email, password, full_name, role, status) VALUES
('admin', 'admin@perfumepalace.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin', 'active');

-- Insert Sample User (password: user123)
INSERT INTO users (name, email, password, phone, email_verified, status) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', 1, 'active');

-- Insert Coupons
INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount_amount, valid_from, valid_until, is_active) VALUES
('WELCOME10', 'Welcome discount for new users', 'percentage', 10.00, 1000.00, 500.00, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
('SAVE500', 'Flat 500 off on orders above 3000', 'fixed', 500.00, 3000.00, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 1),
('LUXURY20', 'Luxury collection 20% off', 'percentage', 20.00, 5000.00, 1000.00, NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY), 1);

-- Insert Banners
INSERT INTO banners (title, subtitle, image, link, button_text, position, display_order, is_active, start_date, end_date) VALUES
('Discover Luxury Fragrances', 'Premium perfumes for every occasion', 'banner-1.jpg', 'products.php', 'Shop Now', 'hero', 1, 1, NOW(), DATE_ADD(NOW(), INTERVAL 90 DAY)),
('New Arrivals', 'Explore our latest collection', 'banner-2.jpg', 'products.php?filter=new', 'Explore', 'hero', 2, 1, NOW(), DATE_ADD(NOW(), INTERVAL 90 DAY)),
('Special Offer', 'Up to 30% off on selected items', 'banner-3.jpg', 'products.php?filter=sale', 'Get Offer', 'hero', 3, 1, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY));

-- Insert Site Settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'Perfume Palace', 'text'),
('site_email', 'info@perfumepalace.com', 'text'),
('site_phone', '+91 9876543210', 'text'),
('shipping_charge', '50', 'number'),
('free_shipping_threshold', '1000', 'number'),
('tax_percentage', '18', 'number'),
('currency_symbol', '₹', 'text'),
('items_per_page', '12', 'number');

-- =====================================================
-- TRIGGERS FOR AUTOMATIC UPDATES
-- =====================================================

-- Update product rating when review is added
DELIMITER $$
CREATE TRIGGER update_product_rating_after_review
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE products 
    SET rating_avg = (SELECT AVG(rating) FROM reviews WHERE product_id = NEW.product_id AND is_approved = 1),
        rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = NEW.product_id AND is_approved = 1)
    WHERE id = NEW.product_id;
END$$

-- Update product stock after order
CREATE TRIGGER update_stock_after_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock - NEW.quantity,
        sales_count = sales_count + NEW.quantity
    WHERE id = NEW.product_id;
END$$

-- Restore stock on order cancellation
CREATE TRIGGER restore_stock_on_cancel
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_status = 'cancelled' AND OLD.order_status != 'cancelled' THEN
        UPDATE products p
        INNER JOIN order_items oi ON p.id = oi.product_id
        SET p.stock = p.stock + oi.quantity
        WHERE oi.order_id = NEW.id;
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- VIEWS FOR REPORTING
-- =====================================================

CREATE VIEW vw_product_details AS
SELECT 
    p.*,
    c.name as category_name,
    b.name as brand_name,
    pi.image_path as primary_image
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1;

CREATE VIEW vw_order_summary AS
SELECT 
    o.*,
    u.name as customer_name,
    u.email as customer_email,
    u.phone as customer_phone,
    COUNT(oi.id) as total_items
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Additional composite indexes
CREATE INDEX idx_product_category_status ON products(category_id, status);
CREATE INDEX idx_product_brand_status ON products(brand_id, status);
CREATE INDEX idx_order_user_status ON orders(user_id, order_status);
CREATE INDEX idx_order_date ON orders(created_at);
CREATE INDEX idx_review_product_approved ON reviews(product_id, is_approved);

-- =====================================================
-- END OF SCHEMA
-- =====================================================
