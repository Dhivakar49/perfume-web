<?php
/**
 * Global Configuration File - InfinityFree Compatible
 */

// Environment Detection (set to 'production' for live site)
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'production');
}

// Error Reporting (Disabled in production, enabled in development)
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Site Configuration - AUTO-DETECT URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_url = $protocol . $host;

define('SITE_NAME', 'Perfume Palace');
if (!defined('SITE_URL')) {
    define('SITE_URL', getenv('SITE_URL') ?: $base_url);
}
define('SITE_EMAIL', 'info@perfumepalace.com');

// Path Configuration - PORTABLE PATHS
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('PRODUCT_IMAGE_PATH', ROOT_PATH . '/assets/images/products/');
define('PROFILE_IMAGE_PATH', UPLOAD_PATH . 'profiles/');

// URL Configuration - DYNAMIC URLS
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOAD_URL', SITE_URL . '/assets/uploads');
define('IMAGES_URL', SITE_URL . '/assets/images');

// Security
if (!defined('CSRF_TOKEN_NAME')) {
    define('CSRF_TOKEN_NAME', 'csrf_token');
}
define('SESSION_LIFETIME', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 12);

// Currency
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', '₹');
}
if (!defined('CURRENCY_CODE')) {
    define('CURRENCY_CODE', 'INR');
}

// Shipping
define('SHIPPING_CHARGE', 50);
define('FREE_SHIPPING_THRESHOLD', 1000);

// Tax
define('TAX_PERCENTAGE', 18);

// Payment Gateway (Add your keys)
define('RAZORPAY_KEY_ID', getenv('RAZORPAY_KEY_ID') ?: 'your_razorpay_key');
define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: 'your_razorpay_secret');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'your_email@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'your_password');

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
if (file_exists(ROOT_PATH . '/includes/functions.php')) {
    require_once ROOT_PATH . '/includes/functions.php';
}
?>
