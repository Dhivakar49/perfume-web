<?php
session_start();

// Simple admin authentication (you can enhance this later)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Get dashboard statistics
try {
    $db = Database::getInstance()->getConnection();
    
    // Total products
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $total_products = $stmt->fetch()['count'];
    
    // Total orders
    $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
    $total_orders = $stmt->fetch()['count'];
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $total_users = $stmt->fetch()['count'];
    
    // Total revenue
    $stmt = $db->query("SELECT SUM(total_amount) as revenue FROM orders WHERE payment_status = 'paid'");
    $total_revenue = $stmt->fetch()['revenue'] ?? 0;
    
    // Recent orders
    $stmt = $db->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Perfume Palace</title>
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
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .content-area {
            padding: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-icon.products { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-icon.orders { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .stat-icon.users { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .stat-icon.revenue { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
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
        }
        
        .table {
            margin: 0;
        }
        
        .table th {
            border-top: none;
            color: #666;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #c9a961;
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
            <a href="index.php" class="active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php">
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
        <!-- Top Navbar -->
        <div class="top-navbar">
            <h4>Dashboard</h4>
            <div class="admin-profile">
                <span>Admin</span>
                <div class="admin-avatar">A</div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon products">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-value"><?php echo $total_products; ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-value"><?php echo $total_orders; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value"><?php echo $total_users; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-value">₹<?php echo number_format($total_revenue, 0); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clock me-2"></i> Recent Orders
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_orders)): ?>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                            <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $order['status'] === 'completed' ? 'success' : 
                                                        ($order['status'] === 'pending' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No orders yet</td>
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
