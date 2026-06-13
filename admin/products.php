<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete' && isset($_POST['product_id'])) {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_POST['product_id']]);
            $success = "Product deleted successfully";
        }
        
        if ($_POST['action'] === 'toggle_status' && isset($_POST['product_id'])) {
            $stmt = $db->prepare("UPDATE products SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
            $stmt->execute([$_POST['product_id']]);
            $success = "Product status updated";
        }
    }
}

// Get all products
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("
        SELECT p.*, pi.image_path, c.name as category_name, b.name as brand_name 
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #d4af37;
            --secondary: #2c2c2c;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            border-left-color: var(--primary);
            color: white;
        }
        
        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
        }
        
        .top-navbar {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-navbar h4 {
            margin: 0;
            color: var(--secondary);
            font-weight: 600;
        }
        
        .content-area {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 20px 25px;
            font-weight: 600;
            color: var(--secondary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-spa"></i> Perfume Palace</h3>
            <small style="opacity: 0.8;">Admin Panel</small>
        </div>
        <div class="sidebar-menu">
            <a href="index.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php" class="active">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="orders.php">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="users.php">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="categories.php">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="settings.php">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 15px;">
            <a href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>View Store</span>
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h4>Products Management</h4>
        </div>

        <div class="content-area">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-box me-2"></i> All Products</span>
                    <button class="btn btn-primary btn-sm" onclick="alert('Add Product feature coming soon!')">
                        <i class="fas fa-plus me-2"></i> Add Product
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <?php if ($product['image_path']): ?>
                                                    <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product">
                                                <?php else: ?>
                                                    <div style="width:50px;height:50px;background:#ddd;border-radius:8px;"></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                                            <td><strong>₹<?php echo number_format($product['price'], 2); ?></strong></td>
                                            <td><?php echo $product['stock_quantity']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($product['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Toggle Status">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No products found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
