<?php
/**
 * Database Configuration - InfinityFree Compatible
 * Professional PDO Connection with Environment Support
 */

// Database Configuration Constants
// CHANGE THESE VALUES FOR YOUR INFINITYFREE ACCOUNT
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'sql311.infinityfree.com');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'if0_42171606_perfume');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'if0_42171606');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: 'QjHj5zR1zNp');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false, // InfinityFree may have connection limits
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch(PDOException $e) {
            // Log error for debugging
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Show different errors for development vs production
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                die(json_encode([
                    'success' => false, 
                    'message' => 'Database connection failed: ' . $e->getMessage(),
                    'host' => DB_HOST,
                    'database' => DB_NAME,
                    'user' => DB_USER
                ]));
            } else {
                die(json_encode([
                    'success' => false, 
                    'message' => 'Database connection failed. Please contact support.'
                ]));
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Test database connection
    public function testConnection() {
        try {
            $stmt = $this->conn->query("SELECT 1");
            return ['success' => true, 'message' => 'Database connected successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
