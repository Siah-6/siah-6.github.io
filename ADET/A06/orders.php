<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user orders
function getUserOrders($conn, $userId) {
    $query = "
        SELECT r.id, r.total, r.created_at, r.completed, COUNT(ri.product_id) as item_count,
               GROUP_CONCAT(CONCAT(p.name, ' (', ri.quantity, ')') SEPARATOR ', ') as items
        FROM receipts r
        LEFT JOIN receipt_items ri ON r.id = ri.receipt_id
        LEFT JOIN products p ON ri.product_id = p.id
        WHERE r.completed = 1 AND r.user_id = ?
        GROUP BY r.id, r.total, r.created_at, r.completed
        ORDER BY r.created_at DESC
        LIMIT 20
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    return $orders;
}

// Get order statistics
function getOrderStats($conn, $userId) {
    $stats = [];
    
    // Total orders for this user
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM receipts WHERE completed = 1 AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $stats['total_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Total spent by this user
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(total), 0) as total FROM receipts WHERE completed = 1 AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $stats['total_spent'] = mysqli_fetch_assoc($result)['total'];
    
    // This month's orders for this user
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM receipts WHERE completed = 1 AND user_id = ? AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $stats['this_month_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Favorite category for this user
    $stmt = mysqli_prepare($conn, "
        SELECT p.category, COUNT(*) as order_count 
        FROM receipt_items ri 
        JOIN products p ON ri.product_id = p.id 
        JOIN receipts r ON ri.receipt_id = r.id 
        WHERE r.completed = 1 AND r.user_id = ?
        GROUP BY p.category 
        ORDER BY order_count DESC 
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $favorite = mysqli_fetch_assoc($result);
    $stats['favorite_category'] = $favorite ? $favorite['category'] : 'None';
    
    return $stats;
}

$orders = getUserOrders($conn, $_SESSION['user_id']);
$stats = getOrderStats($conn, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Legend Brews</title>
    
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
            --coffee-cream: #f5e6d3;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--coffee-cream) 0%, #faf6f2 100%);
            min-height: 100vh;
        }
        
        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Page Header */
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #666;
            margin-bottom: 0;
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
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .orders-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .filter-btn {
            padding: 0.4rem 0.8rem;
            border: 1px solid rgba(255,255,255,0.3);
            background: transparent;
            color: white;
            border-radius: 20px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(255,255,255,0.2);
            border-color: white;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--coffee-dark);
            border-bottom: 2px solid #e9ecef;
        }
        
        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .orders-table tbody tr:hover {
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
        
        .order-status.processing {
            background: #cce7ff;
            color: #004085;
        }
        
        .order-items {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .order-total {
            font-weight: 600;
            color: var(--coffee-dark);
        }
        
        .order-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            padding: 0.4rem 0.8rem;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }
        
        .btn-action:hover {
            background: var(--coffee-cream);
            border-color: var(--coffee-light);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--coffee-light);
            margin-bottom: 1rem;
        }
        
        .empty-state h4 {
            color: var(--coffee-dark);
            margin-bottom: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .orders-table {
                font-size: 0.9rem;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 0.8rem 0.5rem;
            }
            
            .order-items {
                max-width: 150px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mug-hot"></i>
                Legend Brews
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php">
                            <i class="fas fa-receipt me-1"></i> My Orders
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-cog me-2"></i> Profile
                            </a></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">My Orders</h1>
            <p class="page-subtitle">Track your order history and manage your purchases</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_spent'], 2); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['this_month_orders']); ?></div>
                <div class="stat-label">This Month</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-value"><?php echo ucfirst($stats['favorite_category']); ?></div>
                <div class="stat-label">Favorite Category</div>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="orders-container">
            <div class="orders-header">
                <h3 class="orders-title">Order History</h3>
                <div class="filter-buttons">
                    <button class="filter-btn active" onclick="filterOrders('all')">All Orders</button>
                    <button class="filter-btn" onclick="filterOrders('completed')">Completed</button>
                    <button class="filter-btn" onclick="filterOrders('processing')">Processing</button>
                    <button class="filter-btn" onclick="filterOrders('pending')">Pending</button>
                </div>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <h4>No Orders Yet</h4>
                    <p>You haven't placed any orders yet. Start shopping to see your order history here!</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <?php foreach ($orders as $order): ?>
                            <tr data-status="completed">
                                <td>
                                    <span class="order-id">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td>
                                    <div class="order-items" title="<?php echo htmlspecialchars($order['items']); ?>">
                                        <?php echo htmlspecialchars($order['items']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="order-total">₱<?php echo number_format($order['total'], 2); ?></span>
                                </td>
                                <td>
                                    <div class="order-date">
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        <br>
                                        <small><?php echo date('h:i A', strtotime($order['created_at'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="order-status completed">Completed</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                        <button class="btn-action" onclick="reorderItems(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-redo me-1"></i> Reorder
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadReceipt()">
                        <i class="fas fa-download me-2"></i> Download Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Filter orders
        function filterOrders(status) {
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Filter table rows
            const rows = document.querySelectorAll('#ordersTableBody tr');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // View order details
        function viewOrderDetails(orderId) {
            // Simulate loading order details
            const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            const content = document.getElementById('orderDetailsContent');
            
            content.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading order details...</p>
                </div>
            `;
            
            modal.show();
            
            // Simulate API call
            setTimeout(() => {
                content.innerHTML = `
                    <div class="order-details">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Order ID:</strong> #${String(orderId).padStart(6, '0')}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong> <span class="badge bg-success">Completed</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Order Date:</strong> ${new Date().toLocaleDateString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Payment Method:</strong> Cash on Delivery
                            </div>
                        </div>
                        <hr>
                        <h6>Items Ordered:</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cappuccino</td>
                                        <td>2</td>
                                        <td>₱120.00</td>
                                        <td>₱240.00</td>
                                    </tr>
                                    <tr>
                                        <td>Blueberry Muffin</td>
                                        <td>1</td>
                                        <td>₱65.00</td>
                                        <td>₱65.00</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total:</th>
                                        <th>₱305.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Delivery Address:</strong><br>
                                123 Coffee Street<br>
                                Brew City, BC 12345
                            </div>
                            <div class="col-md-6">
                                <strong>Contact:</strong><br>
                                +63 912 345 6789<br>
                                customer@example.com
                            </div>
                        </div>
                    </div>
                `;
            }, 1000);
        }
        
        // Reorder items
        function reorderItems(orderId) {
            if (confirm('Add all items from this order to your cart?')) {
                // Simulate adding to cart
                alert('Items added to cart! Redirecting to checkout...');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            }
        }
        
        // Download receipt
        function downloadReceipt() {
            alert('Receipt download would be implemented here with PDF generation');
        }
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', () => {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
