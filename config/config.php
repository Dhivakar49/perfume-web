<?php
/**
 * Global Configuration File
 */

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Site Configuration
define('SITE_NAME', 'Perfume Palace');
define('SITE_URL', 'http://localhost:8000');
define('SITE_EMAIL', 'info@perfumepalace.com');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('PRODUCT_IMAGE_PATH', UPLOAD_PATH . 'products/');
define('PROFILE_IMAGE_PATH', UPLOAD_PATH . 'profiles/');

// URL Configuration
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOAD_URL', SITE_URL . '/assets/uploads');

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 12);

// Currency
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');

// Shipping
define('SHIPPING_CHARGE', 50);
define('FREE_SHIPPING_THRESHOLD', 1000);

// Tax
define('TAX_PERCENTAGE', 18);

// Payment Gateway (Add your keys)
define('RAZORPAY_KEY_ID', 'your_razorpay_key');
define('RAZORPAY_KEY_SECRET', 'your_razorpay_secret');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_password');

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);

// Auto-load classes
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/includes/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Include helper functions
require_once ROOT_PATH . '/includes/functions.php';
?>
