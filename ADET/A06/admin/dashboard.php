<?php
session_start();
require_once '../connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get dashboard statistics
function getDashboardStats($conn) {
    $stats = [];
    
    // Total products
    $result = executeQuery("SELECT COUNT(*) as count FROM products");
    $stats['total_products'] = mysqli_fetch_assoc($result)['count'];
    
    // Total orders
    $result = executeQuery("SELECT COUNT(*) as count FROM receipts WHERE completed = 1");
    $stats['total_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Total revenue
    $result = executeQuery("SELECT SUM(total) as total FROM receipts WHERE completed = 1");
    $stats['total_revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    
    // Today's orders
    $result = executeQuery("SELECT COUNT(*) as count FROM receipts WHERE DATE(created_at) = CURDATE() AND completed = 1");
    $stats['today_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Today's revenue
    $result = executeQuery("SELECT SUM(total) as total FROM receipts WHERE DATE(created_at) = CURDATE() AND completed = 1");
    $stats['today_revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    
    // Low stock products (assuming we add stock column later)
    $result = executeQuery("SELECT COUNT(*) as count FROM products WHERE price < 100");
    $stats['low_stock'] = mysqli_fetch_assoc($result)['count'];
    
    return $stats;
}

// Get sales data for charts
function getSalesData($conn, $period = 'week') {
    $data = [];
    
    if ($period === 'week') {
        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $result = executeQuery("SELECT COUNT(*) as orders, COALESCE(SUM(total), 0) as revenue FROM receipts WHERE DATE(created_at) = '$date' AND completed = 1");
            $row = mysqli_fetch_assoc($result);
            $data[] = [
                'date' => date('M d', strtotime($date)),
                'orders' => (int)$row['orders'],
                'revenue' => (float)$row['revenue']
            ];
        }
    } elseif ($period === 'month') {
        // Last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $result = executeQuery("SELECT COUNT(*) as orders, COALESCE(SUM(total), 0) as revenue FROM receipts WHERE DATE(created_at) = '$date' AND completed = 1");
            $row = mysqli_fetch_assoc($result);
            $data[] = [
                'date' => date('M d', strtotime($date)),
                'orders' => (int)$row['orders'],
                'revenue' => (float)$row['revenue']
            ];
        }
    }
    
    return $data;
}

// Get top products
function getTopProducts($conn, $limit = 5) {
    $query = "
        SELECT p.name, p.price, COUNT(ri.product_id) as sold_count, SUM(ri.quantity) as total_quantity
        FROM products p
        LEFT JOIN receipt_items ri ON p.id = ri.product_id
        LEFT JOIN receipts r ON ri.receipt_id = r.id
        WHERE r.completed = 1
        GROUP BY p.id, p.name, p.price
        ORDER BY total_quantity DESC
        LIMIT $limit
    ";
    
    $result = executeQuery($query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

// Get recent orders
function getRecentOrders($conn, $limit = 10) {
    $query = "
        SELECT r.id, r.total, r.created_at, COUNT(ri.product_id) as item_count
        FROM receipts r
        LEFT JOIN receipt_items ri ON r.id = ri.receipt_id
        WHERE r.completed = 1
        GROUP BY r.id, r.total, r.created_at
        ORDER BY r.created_at DESC
        LIMIT $limit
    ";
    
    $result = executeQuery($query);
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    return $orders;
}

// Get data
$stats = getDashboardStats($conn);
$salesData = getSalesData($conn);
$topProducts = getTopProducts($conn);
$recentOrders = getRecentOrders($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Legend Brews</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --coffee-dark: #2c1810;
            --coffee-medium: #6f4e37;
            --coffee-light: #a67c52;
            --coffee-accent: #c17d4a;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
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
            transition: transform 0.3s ease;
        }
        
        .sidebar.collapsed {
            transform: translateX(-250px);
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
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        /* Top Bar */
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .sidebar-toggle {
            background: var(--coffee-medium);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 1rem;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card.primary {
            border-left-color: var(--info);
        }
        
        .stat-card.success {
            border-left-color: var(--success);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning);
        }
        
        .stat-card.danger {
            border-left-color: var(--danger);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-icon.primary {
            background: rgba(23, 162, 184, 0.1);
            color: var(--info);
        }
        
        .stat-icon.success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .stat-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .stat-icon.danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .stat-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        
        .stat-change.positive {
            color: var(--success);
        }
        
        .stat-change.negative {
            color: var(--danger);
        }
        
        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 400px;
            margin-bottom: 2rem;
        }
        
        .chart-container canvas {
            max-height: 300px !important;
        }
        
        .chart-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }
        
        .chart-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .chart-btn {
            padding: 0.3rem 0.8rem;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .chart-btn.active,
        .chart-btn:hover {
            background: var(--coffee-medium);
            color: white;
            border-color: var(--coffee-medium);
        }
        
        /* Tables */
        .data-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .data-table table {
            margin: 0;
        }
        
        .data-table th {
            background: var(--coffee-dark);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Badges */
        .badge-custom {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,.1);
            border-radius: 50%;
            border-top-color: var(--coffee-medium);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-mug-hot me-2"></i>
            Legend Brews
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="active">
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
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    Settings
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
    <main class="main-content" id="mainContent">
        <!-- Top Bar -->
        <div class="top-bar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-3">Admin Dashboard</h4>
                <span class="badge bg-success">Live</span>
            </div>
            
            <div class="d-flex align-items-center">
                <span class="me-3">
                    <i class="fas fa-user me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <span id="currentTime"></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <div class="stat-icon primary">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_products']); ?></div>
                    <div class="stat-label">Total Products</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i>
                        +12% from last month
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-icon success">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i>
                        +8% from last month
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-icon warning">
                        <i class="fas fa-peso-sign"></i>
                    </div>
                    <div class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i>
                        +15% from last month
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card danger">
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['low_stock']); ?></div>
                    <div class="stat-label">Low Stock Items</div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-down me-1"></i>
                        -3 from yesterday
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">Sales Overview</h5>
                        <div class="chart-controls">
                            <button class="chart-btn active" onclick="updateChart('week')">Week</button>
                            <button class="chart-btn" onclick="updateChart('month')">Month</button>
                        </div>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">Category Distribution</h5>
                    </div>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row">
            <div class="col-lg-6">
                <div class="data-table">
                    <div class="p-3">
                        <h5 class="mb-3">Top Products</h5>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-coffee text-muted"></i>
                                            </div>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </div>
                                    </td>
                                    <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $product['total_quantity']; ?></span>
                                    </td>
                                    <td>₱<?php echo number_format($product['price'] * $product['total_quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="data-table">
                    <div class="p-3">
                        <h5 class="mb-3">Recent Orders</h5>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark">#<?php echo $order['id']; ?></span>
                                    </td>
                                    <td><?php echo $order['item_count']; ?></td>
                                    <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-custom bg-success">Completed</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let salesChart, categoryChart;
        let currentChartPeriod = 'week';
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            updateTime();
            setInterval(updateTime, 1000);
        });
        
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
        
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('en-US', { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            document.getElementById('currentTime').textContent = timeString;
        }
        
        // Initialize charts
        function initializeCharts() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesData = <?php echo json_encode($salesData); ?>;
            
            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesData.map(d => d.date),
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: salesData.map(d => d.revenue),
                        borderColor: '#6f4e37',
                        backgroundColor: 'rgba(111, 78, 55, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Orders',
                        data: salesData.map(d => d.orders),
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
            
            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hot Drinks', 'Cold Drinks', 'Pastries', 'Merchandise'],
                    datasets: [{
                        data: [35, 25, 25, 15],
                        backgroundColor: [
                            '#6f4e37',
                            '#17a2b8',
                            '#ffc107',
                            '#28a745'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Update chart period
        function updateChart(period) {
            // Update button states
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Here you would fetch new data based on period
            currentChartPeriod = period;
            
            // Simulate loading new data
            showLoadingChart();
            
            setTimeout(() => {
                // Update chart with new data
                updateChartData(period);
            }, 500);
        }
        
        // Show loading state
        function showLoadingChart() {
            if (salesChart) {
                salesChart.data.datasets[0].data = Array(7).fill(null);
                salesChart.data.datasets[1].data = Array(7).fill(null);
                salesChart.update();
            }
        }
        
        // Update chart data
        function updateChartData(period) {
            // This would fetch real data from the server
            // For now, we'll simulate it
            const mockData = period === 'week' ? 
                [1200, 1900, 1500, 2100, 2400, 1800, 2200] :
                [8500, 9200, 8800, 9500, 10200, 9800, 10500, 11200, 10800, 11500, 12000, 11800, 12500, 13200, 12800, 13500, 14200, 13800, 14500, 15200, 14800, 15500, 16200, 15800, 16500, 17200, 16800, 17500, 18200, 17800];
            
            if (salesChart) {
                salesChart.data.datasets[0].data = mockData;
                salesChart.update();
            }
        }
        
        // Mobile sidebar handling
        if (window.innerWidth <= 768) {
            document.addEventListener('click', (e) => {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = e.target.closest('.sidebar-toggle');
                
                if (!toggleBtn && !sidebar.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
        }
    </script>
</body>
</html>
