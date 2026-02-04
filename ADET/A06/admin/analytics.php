<?php
session_start();
require_once '../connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get analytics data
function getAnalyticsData($conn) {
    $data = [];
    
    // Sales by category
    $query = "
        SELECT p.category, COUNT(ri.quantity) as total_quantity, SUM(ri.price * ri.quantity) as total_revenue
        FROM receipt_items ri
        JOIN products p ON ri.product_id = p.id
        JOIN receipts r ON ri.receipt_id = r.id
        WHERE r.completed = 1
        GROUP BY p.category
        ORDER BY total_revenue DESC
    ";
    $result = executeQuery($query);
    $data['category_sales'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['category_sales'][] = $row;
    }
    
    // Monthly sales trend
    $query = "
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as orders, SUM(total) as revenue
        FROM receipts
        WHERE completed = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month
    ";
    $result = executeQuery($query);
    $data['monthly_trend'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['monthly_trend'][] = $row;
    }
    
    // Top products
    $query = "
        SELECT p.name, SUM(ri.quantity) as total_sold, SUM(ri.price * ri.quantity) as revenue
        FROM receipt_items ri
        JOIN products p ON ri.product_id = p.id
        JOIN receipts r ON ri.receipt_id = r.id
        WHERE r.completed = 1
        GROUP BY p.id, p.name
        ORDER BY total_sold DESC
        LIMIT 10
    ";
    $result = executeQuery($query);
    $data['top_products'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['top_products'][] = $row;
    }
    
    // Hourly sales pattern
    $query = "
        SELECT HOUR(created_at) as hour, COUNT(*) as orders, SUM(total) as revenue
        FROM receipts
        WHERE completed = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY HOUR(created_at)
        ORDER BY hour
    ";
    $result = executeQuery($query);
    $data['hourly_pattern'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['hourly_pattern'][] = $row;
    }
    
    return $data;
}

$analytics = getAnalyticsData($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Legend Brews Admin</title>
    
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
        
        /* Page Header */
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        /* Chart Containers */
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 400px;
        }
        
        .chart-container canvas {
            max-height: 300px !important;
        }
        
        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 1rem;
        }
        
        /* Analytics Cards */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .analytics-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .analytics-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 1rem;
        }
        
        .top-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .top-item:last-child {
            border-bottom: none;
        }
        
        .top-item-name {
            font-weight: 500;
            color: var(--coffee-dark);
        }
        
        .top-item-value {
            font-weight: 600;
            color: var(--coffee-medium);
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
            
            .chart-grid {
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
                <a href="customers.php">
                    <i class="fas fa-users"></i>
                    Customers
                </a>
            </li>
            <li>
                <a href="analytics.php" class="active">
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
        <div class="page-header">
            <h1 class="page-title">Analytics & Insights</h1>
            <p class="text-muted mb-0">Comprehensive business analytics and performance metrics</p>
        </div>
        
        <!-- Charts Grid -->
        <div class="chart-grid">
            <!-- Category Sales Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Sales by Category</h3>
                <canvas id="categoryChart"></canvas>
            </div>
            
            <!-- Monthly Trend Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Monthly Sales Trend</h3>
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        
        <!-- Analytics Grid -->
        <div class="analytics-grid">
            <!-- Top Products -->
            <div class="analytics-card">
                <h3 class="analytics-title">
                    <i class="fas fa-trophy me-2"></i>Top Selling Products
                </h3>
                <div id="topProductsList">
                    <?php if (empty($analytics['top_products'])): ?>
                        <p class="text-muted">No sales data available</p>
                    <?php else: ?>
                        <?php foreach ($analytics['top_products'] as $index => $product): ?>
                            <div class="top-item">
                                <span class="top-item-name">
                                    <?php echo ($index + 1) . '. ' . htmlspecialchars($product['name']); ?>
                                </span>
                                <span class="top-item-value">
                                    <?php echo $product['total_sold']; ?> sold
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Category Performance -->
            <div class="analytics-card">
                <h3 class="analytics-title">
                    <i class="fas fa-chart-pie me-2"></i>Category Performance
                </h3>
                <div id="categoryPerformance">
                    <?php if (empty($analytics['category_sales'])): ?>
                        <p class="text-muted">No sales data available</p>
                    <?php else: ?>
                        <?php foreach ($analytics['category_sales'] as $category): ?>
                            <div class="top-item">
                                <span class="top-item-name">
                                    <?php echo ucfirst(htmlspecialchars($category['category'])); ?>
                                </span>
                                <span class="top-item-value">
                                    ₱<?php echo number_format($category['total_revenue'], 2); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Peak Hours -->
            <div class="analytics-card">
                <h3 class="analytics-title">
                    <i class="fas fa-clock me-2"></i>Peak Sales Hours
                </h3>
                <div id="peakHours">
                    <?php if (empty($analytics['hourly_pattern'])): ?>
                        <p class="text-muted">No sales data available</p>
                    <?php else: ?>
                        <?php 
                        // Sort by orders and get top 5
                        usort($analytics['hourly_pattern'], function($a, $b) {
                            return $b['orders'] - $a['orders'];
                        });
                        $topHours = array_slice($analytics['hourly_pattern'], 0, 5);
                        ?>
                        <?php foreach ($topHours as $hour): ?>
                            <div class="top-item">
                                <span class="top-item-name">
                                    <?php echo sprintf('%02d:00', $hour['hour']); ?> - <?php echo sprintf('%02d:00', ($hour['hour'] + 1) % 24); ?>
                                </span>
                                <span class="top-item-value">
                                    <?php echo $hour['orders']; ?> orders
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Additional Charts -->
        <div class="chart-container">
            <h3 class="chart-title">Hourly Sales Pattern</h3>
            <canvas id="hourlyChart"></canvas>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Analytics data from PHP
        const analyticsData = <?php echo json_encode($analytics); ?>;
        
        // Initialize charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeCategoryChart();
            initializeTrendChart();
            initializeHourlyChart();
        });
        
        // Category Sales Chart
        function initializeCategoryChart() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            const categoryData = analyticsData.category_sales || [];
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.category),
                    datasets: [{
                        data: categoryData.map(item => item.total_revenue),
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
        
        // Monthly Trend Chart
        function initializeTrendChart() {
            const ctx = document.getElementById('trendChart').getContext('2d');
            const trendData = analyticsData.monthly_trend || [];
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendData.map(item => {
                        const date = new Date(item.month + '-01');
                        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: trendData.map(item => item.revenue),
                        borderColor: '#6f4e37',
                        backgroundColor: 'rgba(111, 78, 55, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Hourly Sales Pattern Chart
        function initializeHourlyChart() {
            const ctx = document.getElementById('hourlyChart').getContext('2d');
            const hourlyData = analyticsData.hourly_pattern || [];
            
            // Fill missing hours with 0
            const fullDayData = [];
            for (let hour = 0; hour < 24; hour++) {
                const hourData = hourlyData.find(item => item.hour == hour);
                fullDayData.push({
                    hour: hour,
                    orders: hourData ? hourData.orders : 0,
                    revenue: hourData ? hourData.revenue : 0
                });
            }
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: fullDayData.map(item => item.hour + ':00'),
                    datasets: [{
                        label: 'Orders',
                        data: fullDayData.map(item => item.orders),
                        backgroundColor: '#17a2b8',
                        borderColor: '#17a2b8',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
