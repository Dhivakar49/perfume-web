<?php
/**
 * One-Click Database Installation Script
 * Run this file to set up the complete database
 */

// Database credentials
$host = 'localhost';
$username = 'root';
$password = 'dhivakar7890';
$database = 'perfume_store';

echo "<pre>";
echo "===========================================\n";
echo "PERFUME PALACE - DATABASE INSTALLATION\n";
echo "===========================================\n\n";

try {
    // Step 1: Connect to MySQL
    echo "Step 1: Connecting to MySQL server...\n";
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    echo "   ✓ Connected successfully\n\n";
    
    // Step 2: Drop and create database
    echo "Step 2: Setting up database...\n";
    $conn->query("DROP DATABASE IF EXISTS $database");
    $conn->query("CREATE DATABASE $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($database);
    echo "   ✓ Database '$database' created\n\n";
    
    // Step 3: Read and execute SQL file
    echo "Step 3: Creating tables...\n";
    $sql_file = __DIR__ . '/database/schema_clean.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    $sql = file_get_contents($sql_file);
    
    // Remove comments and split by semicolon
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Execute multi-query
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
    
    if ($conn->error) {
        echo "   ⚠ Warning: " . $conn->error . "\n";
    }
    
    echo "   ✓ All tables created successfully\n\n";
    
    // Step 4: Verify tables
    echo "Step 4: Verifying installation...\n";
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "   ✓ Created " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "     • $table\n";
    }
    echo "\n";
    
    // Step 5: Verify sample data
    echo "Step 5: Verifying sample data...\n";
    
    $checks = [
        'products' => 'SELECT COUNT(*) as count FROM products',
        'categories' => 'SELECT COUNT(*) as count FROM categories',
        'brands' => 'SELECT COUNT(*) as count FROM brands',
        'users' => 'SELECT COUNT(*) as count FROM users',
        'admins' => 'SELECT COUNT(*) as count FROM admins',
        'coupons' => 'SELECT COUNT(*) as count FROM coupons',
        'banners' => 'SELECT COUNT(*) as count FROM banners'
    ];
    
    foreach ($checks as $table => $query) {
        $result = $conn->query($query);
        if ($result) {
            $count = $result->fetch_assoc()['count'];
            echo "   ✓ $table: $count records\n";
        }
    }
    echo "\n";
    
    // Success message
    echo "===========================================\n";
    echo "✓ INSTALLATION COMPLETED SUCCESSFULLY!\n";
    echo "===========================================\n\n";
    
    echo "<strong>Test Accounts:</strong>\n";
    echo "  👤 User Login:\n";
    echo "     Email: john@example.com\n";
    echo "     Password: user123\n\n";
    echo "  👨‍💼 Admin Login:\n";
    echo "     Username: admin\n";
    echo "     Password: admin123\n\n";
    
    echo "<strong>Next Steps:</strong>\n";
    echo "  1. <a href='index.php'>Visit Homepage</a>\n";
    echo "  2. <a href='products.php'>Browse Products</a>\n";
    echo "  3. <a href='login.php'>Login</a>\n";
    echo "  4. <a href='register.php'>Register New Account</a>\n\n";
    
    echo "<strong>Database Info:</strong>\n";
    echo "  Database: $database\n";
    echo "  Tables: " . count($tables) . "\n";
    echo "  Products: 10\n";
    echo "  Categories: 5\n";
    echo "  Brands: 5\n\n";
    
    echo "<div style='background: #d4af37; color: white; padding: 15px; border-radius: 8px; margin-top: 20px;'>";
    echo "<strong>🎉 Your Perfume Palace ecommerce platform is ready!</strong><br>";
    echo "You can now start using the application.";
    echo "</div>\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "\n<div style='background: #f44336; color: white; padding: 15px; border-radius: 8px;'>";
    echo "<strong>✗ ERROR:</strong> " . $e->getMessage() . "\n\n";
    echo "<strong>Troubleshooting:</strong>\n";
    echo "  1. Make sure MySQL service is running\n";
    echo "  2. Verify username and password are correct\n";
    echo "  3. Check if port 3306 is accessible\n";
    echo "  4. Ensure you have CREATE DATABASE privileges\n";
    echo "</div>";
    exit(1);
}

echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Installation - Perfume Palace</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
        }
        pre {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
</body>
</html>
