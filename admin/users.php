<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Get all users
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #d4af37; --secondary: #2c2c2c; --sidebar-width: 260px; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-width); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar-header { padding: 25px 20px; background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h3 { margin: 0; font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: rgba(255,255,255,0.9); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); border-left-color: var(--primary); color: white; }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: 18px; }
        .main-content { margin-left: var(--sidebar-width); padding: 0; }
        .top-navbar { background: white; padding: 20px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .top-navbar h4 { margin: 0; color: var(--secondary); font-weight: 600; }
        .content-area { padding: 30px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card-header { background: white; border-bottom: 1px solid #e0e0e0; padding: 20px 25px; font-weight: 600; color: var(--secondary); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-spa"></i> Perfume Palace</h3>
            <small style="opacity: 0.8;">Admin Panel</small>
        </div>
        <div class="sidebar-menu">
            <a href="index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="products.php"><i class="fas fa-box"></i><span>Products</span></a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
            <a href="users.php" class="active"><i class="fas fa-users"></i><span>Users</span></a>
            <a href="categories.php"><i class="fas fa-tags"></i><span>Categories</span></a>
            <a href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 15px;">
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i><span>View Store</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar"><h4>Users Management</h4></div>
        <div class="content-area">
            <div class="card">
                <div class="card-header"><i class="fas fa-users me-2"></i> All Users</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Orders</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): foreach ($users as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>-</td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No users found</td></tr>
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
