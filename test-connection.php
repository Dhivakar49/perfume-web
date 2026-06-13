<?php
/**
 * Database Connection Test
 * Upload this file to test your InfinityFree database connection
 * Access: https://yoursite.infinityfreeapp.com/test-connection.php
 * DELETE THIS FILE after testing!
 */

// Display errors for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Database Connection Test</h1>";
echo "<hr>";

// Load configuration
require_once 'config/database.php';

echo "<h2>1. Configuration Check</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><td><strong>DB_HOST</strong></td><td>" . DB_HOST . "</td></tr>";
echo "<tr><td><strong>DB_NAME</strong></td><td>" . DB_NAME . "</td></tr>";
echo "<tr><td><strong>DB_USER</strong></td><td>" . DB_USER . "</td></tr>";
echo "<tr><td><strong>DB_PASS</strong></td><td>" . (DB_PASS ? '****' . substr(DB_PASS, -3) : '<span style="color:red">EMPTY!</span>') . "</td></tr>";
echo "</table>";
echo "<hr>";

echo "<h2>2. Connection Test</h2>";
try {
    $db = Database::getInstance();
    $result = $db->testConnection();
    
    if ($result['success']) {
        echo "<p style='color: green; font-size: 20px;'>✅ <strong>SUCCESS!</strong> Database connected successfully</p>";
    } else {
        echo "<p style='color: red; font-size: 20px;'>❌ <strong>FAILED!</strong> " . $result['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 20px;'>❌ <strong>ERROR!</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";

echo "<h2>3. Tables Check</h2>";
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p style='color: green;'>✅ Found " . count($tables) . " tables in database</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ No tables found. Please import database/complete_schema.sql</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking tables: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>⚠️ IMPORTANT: Delete this file after testing!</strong></p>";
echo "<p>File location: <code>" . __FILE__ . "</code></p>";
?>
