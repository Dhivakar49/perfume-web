<?php
/**
 * Newsletter Subscribe API
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

$email = sanitize($_POST['email'] ?? '');

if (empty($email) || !isValidEmail($email)) {
    jsonResponse(['success' => false, 'message' => 'Please enter a valid email address']);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if already subscribed
    $stmt = $db->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    $subscriber = $stmt->fetch();
    
    if ($subscriber) {
        if ($subscriber['status'] === 'active') {
            jsonResponse(['success' => false, 'message' => 'You are already subscribed']);
        } else {
            // Reactivate subscription
            $stmt = $db->prepare("UPDATE newsletter_subscribers SET status = 'active', subscribed_at = NOW() WHERE id = ?");
            $stmt->execute([$subscriber['id']]);
            jsonResponse(['success' => true, 'message' => 'Welcome back! Subscription reactivated']);
        }
    } else {
        // New subscription
        $stmt = $db->prepare("INSERT INTO newsletter_subscribers (email, status) VALUES (?, 'active')");
        $stmt->execute([$email]);
        jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
    }
    
} catch (Exception $e) {
    error_log("Newsletter subscribe error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Subscription failed. Please try again.'], 500);
}
?>
