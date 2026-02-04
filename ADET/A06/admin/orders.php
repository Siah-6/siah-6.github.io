<?php
session_start();
require_once '../connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all orders
function getAllOrders($conn) {
    $query = "
        SELECT r.id, r.total, r.created_at, r.completed, COUNT(ri.product_id) as item_count,
               GROUP_CONCAT(CONCAT(p.name, ' (', ri.quantity, ')') SEPARATOR ', ') as items,
               u.username as customer_name
        FROM receipts r
        LEFT JOIN receipt_items ri ON r.id = ri.receipt_id
        LEFT JOIN products p ON ri.product_id = p.id
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.completed = 1
        GROUP BY r.id, r.total, r.created_at, r.completed, u.username
        ORDER BY r.created_at DESC
        LIMIT 50
    ";
    
    $result = executeQuery($query);
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    return $orders;
}

// Get order statistics
function getOrderStats($conn) {
    $stats = [];
    
    // Total orders
    $result = executeQuery("SELECT COUNT(*) as count FROM receipts WHERE completed = 1");
    $stats['total_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Total revenue
    $result = executeQuery("SELECT COALESCE(SUM(total), 0) as total FROM receipts WHERE completed = 1");
    $stats['total_revenue'] = mysqli_fetch_assoc($result)['total'];
    
    // Today's orders
    $result = executeQuery("SELECT COUNT(*) as count FROM receipts WHERE DATE(created_at) = CURDATE() AND completed = 1");
    $stats['today_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Pending orders
    $result = executeQuery("SELECT COUNT(*) as count FROM receipts WHERE completed = 0");
    $stats['pending_orders'] = mysqli_fetch_assoc($result)['count'];
    
    return $stats;
}

$orders = getAllOrders($conn);
$stats = getOrderStats($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Legend Brews Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --coffee-dark: #2c1810;
            --coffee-medium: #6f4e37;
            --coffee-light: #a67c52;
            --coffee-accent: #c17d4a;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: var(--coffee-dark);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            color: white;
            padding: 2rem 0;
            z-index: 1000;
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 2rem;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 3px solid var(--coffee-accent);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--coffee-medium);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(111, 78, 55, 0.1);
            color: var(--coffee-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Orders Table */
        .orders-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .orders-header {
            background: var(--coffee-dark);
            color: white;
            padding: 1.5rem;
        }
        
        .orders-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--coffee-dark);
            border-bottom: 2px solid #e9ecef;
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .order-id {
            font-weight: 600;
            color: var(--coffee-medium);
        }
        
        .order-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .order-status.completed {
            background: #d4edda;
            color: #155724;
        }
        
        .order-status.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .order-total {
            font-weight: 600;
            color: var(--coffee-dark);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }
        
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        
        .btn-view:hover {
            background: #138496;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-edit:hover {
            background: #e0a800;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-mug-hot me-2"></i>
            Legend Brews
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="products.php">
                    <i class="fas fa-box"></i>
                    Products
                </a>
            </li>
            <li>
                <a href="orders.php" class="active">
                    <i class="fas fa-receipt"></i>
                    Orders
                </a>
            </li>
            <li>
                <a href="customers.php">
                    <i class="fas fa-users"></i>
                    Customers
                </a>
            </li>
            <li>
                <a href="analytics.php">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>
            </li>
            <li>
                <a href="inventory.php">
                    <i class="fas fa-warehouse"></i>
                    Inventory
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="fas fa-store"></i>
                    View Shop
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="mb-3">Order Management</h1>
            <p class="text-muted">Manage and track all customer orders</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['today_orders']); ?></div>
                <div class="stat-label">Today's Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['pending_orders']); ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="orders-container">
            <div class="orders-header">
                <h3 class="orders-title">Recent Orders</h3>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No orders found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <span class="order-id">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars(substr($order['items'], 0, 50)) . '...'; ?></small>
                                </td>
                                <td>
                                    <span class="order-total">₱<?php echo number_format($order['total'], 2); ?></span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td>
                                    <span class="order-status completed">Completed</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="updateStatus(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // View order details
        function viewOrder(orderId) {
            alert('Order details for order #' + String(orderId).padStart(6, '0') + ' would be shown here');
        }
        
        // Update order status
        function updateStatus(orderId) {
            const newStatus = prompt('Enter new status (pending/processing/completed):');
            if (newStatus) {
                alert('Order status updated to: ' + newStatus);
            }
        }
    </script>
</body>
</html>
