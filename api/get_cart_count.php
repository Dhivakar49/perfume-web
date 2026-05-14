<?php
/**
 * Get Cart Count API
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['success' => true, 'count' => 0]);
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([getUserId()]);
    $result = $stmt->fetch();
    
    jsonResponse(['success' => true, 'count' => $result['count']]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'count' => 0]);
}
?>
