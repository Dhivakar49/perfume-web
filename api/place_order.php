<?php
/**
 * Place Order API
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to place order', 'redirect' => 'login.php']);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    $user_id = getUserId();
    
    // Get form data
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $address_line1 = sanitize($_POST['address_line1'] ?? '');
    $address_line2 = sanitize($_POST['address_line2'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');
    $country = sanitize($_POST['country'] ?? 'India');
    $payment_method = sanitize($_POST['payment_method'] ?? 'cod');
    $order_notes = sanitize($_POST['order_notes'] ?? '');
    
    // Validation
    if (empty($full_name) || empty($phone) || empty($email) || empty($address_line1) || empty($city) || empty($state) || empty($pincode)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }
    
    // Get cart items
    $stmt = $db->prepare("
        SELECT c.*, p.name, p.price, p.sale_price, p.stock
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
        exit();
    }
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $price = $item['sale_price'] ?? $item['price'];
        $subtotal += $price * $item['quantity'];
        
        // Check stock
        if ($item['stock'] < $item['quantity']) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock for ' . $item['name']]);
            exit();
        }
    }
    
    $shipping_charge = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
    $tax_amount = calculateTax($subtotal);
    $total_amount = $subtotal + $shipping_charge + $tax_amount;
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Generate order number
        $order_number = generateOrderNumber();
        
        // Create shipping address
        $stmt = $db->prepare("
            INSERT INTO shipping_addresses (user_id, full_name, phone, email, address_line1, address_line2, city, state, pincode, country, is_default, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$user_id, $full_name, $phone, $email, $address_line1, $address_line2, $city, $state, $pincode, $country]);
        $shipping_address_id = $db->lastInsertId();
        
        // Create order
        $stmt = $db->prepare("
            INSERT INTO orders (user_id, order_number, shipping_address_id, subtotal, shipping_charge, tax_amount, total_amount, payment_method, payment_status, order_status, order_notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, NOW())
        ");
        $stmt->execute([
            $user_id,
            $order_number,
            $shipping_address_id,
            $subtotal,
            $shipping_charge,
            $tax_amount,
            $total_amount,
            $payment_method,
            $order_notes
        ]);
        $order_id = $db->lastInsertId();
        
        // Create order items and update stock
        foreach ($cart_items as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            
            // Insert order item
            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price, subtotal, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $price,
                $price * $item['quantity']
            ]);
            
            // Update product stock
            $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Clear cart
        $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Create payment record
        $stmt = $db->prepare("
            INSERT INTO payments (order_id, payment_method, amount, status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$order_id, $payment_method, $total_amount]);
        
        // Commit transaction
        $db->commit();
        
        // Log activity
        try {
            logActivity('place_order', 'order', $order_id, "Order placed: $order_number");
        } catch (Exception $e) {
            error_log("Activity log failed: " . $e->getMessage());
        }
        
        // Send notification (optional)
        try {
            sendNotification($user_id, 'order_placed', 'Order Placed Successfully', "Your order $order_number has been placed successfully.", "orders.php?id=$order_id");
        } catch (Exception $e) {
            error_log("Notification failed: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $order_id,
            'order_number' => $order_number
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Place order error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order: ' . $e->getMessage()
    ]);
}
exit();
?>
