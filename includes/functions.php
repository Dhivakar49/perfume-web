<?php
/**
 * Global Helper Functions - InfinityFree Compatible
 * All functions are wrapped with function_exists to prevent duplicates
 */

/**
 * Sanitize input data
 */
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Validate email
 */
if (!function_exists('isValidEmail')) {
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

/**
 * Generate CSRF Token
 */
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
}

/**
 * Verify CSRF Token
 */
if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken($token) {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
}

/**
 * Check if user is logged in
 */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

/**
 * Check if admin is logged in
 */
if (!function_exists('isAdminLoggedIn')) {
    function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }
}

/**
 * Get current user ID
 */
if (!function_exists('getUserId')) {
    function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}

/**
 * Get current user data
 */
if (!function_exists('getUserData')) {
    function getUserData() {
        if (!isLoggedIn()) return null;
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([getUserId()]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
}

/**
 * Get current admin ID
 */
if (!function_exists('getAdminId')) {
    function getAdminId() {
        return $_SESSION['admin_id'] ?? null;
    }
}

/**
 * Redirect to URL
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: " . $url);
        exit();
    }
}

/**
 * Format price
 */
if (!function_exists('formatPrice')) {
    function formatPrice($price) {
        return CURRENCY_SYMBOL . number_format($price, 2);
    }
}

/**
 * Calculate discount percentage
 */
if (!function_exists('calculateDiscountPercentage')) {
    function calculateDiscountPercentage($original_price, $sale_price) {
        if ($original_price <= 0) return 0;
        return round((($original_price - $sale_price) / $original_price) * 100);
    }
}

/**
 * Generate slug from string
 */
if (!function_exists('generateSlug')) {
    function generateSlug($string) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
        return $slug;
    }
}

/**
 * Generate unique order number
 */
if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

/**
 * Generate SKU
 */
if (!function_exists('generateSKU')) {
    function generateSKU($prefix = 'PERF') {
        return $prefix . '-' . strtoupper(substr(uniqid(), -8));
    }
}

/**
 * Upload image file
 */
if (!function_exists('uploadImage')) {
    function uploadImage($file, $destination_path, $allowed_types = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error'];
        }
        
        $allowed_types = $allowed_types ?? ALLOWED_IMAGE_TYPES;
        
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'File size exceeds limit'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $destination_path . $filename;
        
        if (!is_dir($destination_path)) {
            mkdir($destination_path, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
        }
        
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

/**
 * Delete file
 */
if (!function_exists('deleteFile')) {
    function deleteFile($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}

/**
 * Time ago function
 */
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;
        
        $periods = [
            'year' => 31536000,
            'month' => 2592000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1
        ];
        
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference / $value);
                return $time . ' ' . $key . ($time > 1 ? 's' : '') . ' ago';
            }
        }
        
        return 'Just now';
    }
}

/**
 * Truncate text
 */
if (!function_exists('truncateText')) {
    function truncateText($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}

/**
 * Send JSON response
 */
if (!function_exists('jsonResponse')) {
    function jsonResponse($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

/**
 * Log activity
 */
if (!function_exists('logActivity')) {
    function logActivity($action, $entity_type = null, $entity_id = null, $description = null) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO activity_logs (user_id, admin_id, action, entity_type, entity_id, description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $user_id = getUserId();
            $admin_id = getAdminId();
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            $stmt->execute([$user_id, $admin_id, $action, $entity_type, $entity_id, $description, $ip, $user_agent]);
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
}

/**
 * Send notification to user
 */
if (!function_exists('sendNotification')) {
    function sendNotification($user_id, $type, $title, $message, $link = null) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO notifications (user_id, type, title, message, link)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $type, $title, $message, $link]);
            return true;
        } catch (Exception $e) {
            error_log("Notification error: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Get site setting
 */
if (!function_exists('getSiteSetting')) {
    function getSiteSetting($key, $default = null) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            return $result ? $result['setting_value'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
}

/**
 * Calculate cart total
 */
if (!function_exists('calculateCartTotal')) {
    function calculateCartTotal($items) {
        $subtotal = 0;
        foreach ($items as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $subtotal += $price * $item['quantity'];
        }
        return $subtotal;
    }
}

/**
 * Calculate shipping charge
 */
if (!function_exists('calculateShipping')) {
    function calculateShipping($subtotal) {
        if ($subtotal >= FREE_SHIPPING_THRESHOLD) {
            return 0;
        }
        return SHIPPING_CHARGE;
    }
}

/**
 * Calculate tax
 */
if (!function_exists('calculateTax')) {
    function calculateTax($amount) {
        return ($amount * TAX_PERCENTAGE) / 100;
    }
}

/**
 * Validate coupon
 */
if (!function_exists('validateCoupon')) {
    function validateCoupon($code, $subtotal) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT * FROM coupons 
                WHERE code = ? 
                AND is_active = 1 
                AND valid_from <= NOW() 
                AND valid_until >= NOW()
                AND (usage_limit IS NULL OR used_count < usage_limit)
                AND min_order_amount <= ?
            ");
            $stmt->execute([$code, $subtotal]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * Calculate coupon discount
 */
if (!function_exists('calculateCouponDiscount')) {
    function calculateCouponDiscount($coupon, $subtotal) {
        if (!$coupon) return 0;
        
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($subtotal * $coupon['discount_value']) / 100;
            if ($coupon['max_discount_amount'] && $discount > $coupon['max_discount_amount']) {
                $discount = $coupon['max_discount_amount'];
            }
        } else {
            $discount = $coupon['discount_value'];
        }
        
        return min($discount, $subtotal);
    }
}

/**
 * Hash password
 */
if (!function_exists('hashPassword')) {
    function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

/**
 * Verify password
 */
if (!function_exists('verifyPassword')) {
    function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

/**
 * Generate random token
 */
if (!function_exists('generateToken')) {
    function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
}

/**
 * Get order status badge class
 */
if (!function_exists('getOrderStatusClass')) {
    function getOrderStatusClass($status) {
        $classes = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];
        return $classes[$status] ?? 'secondary';
    }
}

/**
 * Get payment status badge class
 */
if (!function_exists('getPaymentStatusClass')) {
    function getPaymentStatusClass($status) {
        $classes = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];
        return $classes[$status] ?? 'secondary';
    }
}
?>
