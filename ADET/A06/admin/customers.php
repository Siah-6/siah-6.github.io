<?php
session_start();
require_once '../connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all customers
function getAllCustomers($conn) {
    $query = "
        SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.phone, u.created_at,
               COUNT(DISTINCT r.id) as order_count,
               COALESCE(SUM(r.total), 0) as total_spent
        FROM users u
        LEFT JOIN receipts r ON u.id = r.user_id AND r.completed = 1
        WHERE u.role = 'customer'
        GROUP BY u.id, u.username, u.email, u.first_name, u.last_name, u.phone, u.created_at
        ORDER BY u.created_at DESC
    ";
    
    $result = executeQuery($query);
    $customers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
    return $customers;
}

// Get customer statistics
function getCustomerStats($conn) {
    $stats = [];
    
    // Total customers
    $result = executeQuery("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
    $stats['total_customers'] = mysqli_fetch_assoc($result)['count'];
    
    // New customers this month
    $result = executeQuery("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)");
    $stats['new_customers'] = mysqli_fetch_assoc($result)['count'];
    
    // Active customers (with orders)
    $result = executeQuery("SELECT COUNT(DISTINCT user_id) as count FROM receipts WHERE completed = 1 AND user_id IS NOT NULL");
    $stats['active_customers'] = mysqli_fetch_assoc($result)['count'];
    
    // Average order value
    $result = executeQuery("SELECT AVG(total) as avg FROM receipts WHERE completed = 1");
    $stats['avg_order_value'] = mysqli_fetch_assoc($result)['avg'] ?? 0;
    
    return $stats;
}

$customers = getAllCustomers($conn);
$stats = getCustomerStats($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Legend Brews Admin</title>
    
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
        
        /* Customers Table */
        .customers-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .customers-header {
            background: var(--coffee-dark);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .customers-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .search-box {
            position: relative;
            width: 300px;
        }
        
        .search-box input {
            padding-left: 2.5rem;
            border-radius: 20px;
            border: none;
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
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
        
        .customer-name {
            font-weight: 600;
            color: var(--coffee-dark);
        }
        
        .customer-email {
            color: #666;
            font-size: 0.9rem;
        }
        
        .customer-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .customer-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .customer-badge.inactive {
            background: #f8d7da;
            color: #721c24;
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
                <a href="orders.php">
                    <i class="fas fa-receipt"></i>
                    Orders
                </a>
            </li>
            <li>
                <a href="customers.php" class="active">
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
            <h1 class="mb-3">Customer Management</h1>
            <p class="text-muted">Manage and view all customer accounts</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_customers']); ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['new_customers']); ?></div>
                <div class="stat-label">New This Month</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['active_customers']); ?></div>
                <div class="stat-label">Active Customers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['avg_order_value'], 2); ?></div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>
        
        <!-- Customers Table -->
        <div class="customers-container">
            <div class="customers-header">
                <h3 class="customers-title">Customer List</h3>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Search customers..." id="searchInput">
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="customersTableBody">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No customers found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="customer-name">
                                        <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                                    </div>
                                    <div class="customer-email">
                                        <?php echo htmlspecialchars($customer['username']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo htmlspecialchars($customer['email']); ?>
                                    </div>
                                    <?php if ($customer['phone']): ?>
                                        <div>
                                            <i class="fas fa-phone me-2"></i>
                                            <?php echo htmlspecialchars($customer['phone']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo number_format($customer['order_count']); ?></strong>
                                </td>
                                <td>
                                    <strong>₱<?php echo number_format($customer['total_spent'], 2); ?></strong>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                                </td>
                                <td>
                                    <span class="customer-badge <?php echo $customer['order_count'] > 0 ? 'active' : 'inactive'; ?>">
                                        <?php echo $customer['order_count'] > 0 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="viewCustomer(<?php echo $customer['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editCustomer(<?php echo $customer['id']; ?>)">
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
        // Search customers
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customersTableBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // View customer details
        function viewCustomer(customerId) {
            alert('Customer details for ID ' + customerId + ' would be shown here');
        }
        
        // Edit customer
        function editCustomer(customerId) {
            alert('Edit customer functionality for ID ' + customerId + ' would be implemented here');
        }
    </script>
</body>
</html>
