<?php
session_start();
require_once '../connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get inventory data
function getInventoryData($conn) {
    $query = "
        SELECT p.id, p.name, p.category, p.price, p.stock, p.image_url,
               COUNT(ri.product_id) as times_sold,
               SUM(ri.quantity) as total_sold
        FROM products p
        LEFT JOIN receipt_items ri ON p.id = ri.product_id
        LEFT JOIN receipts r ON ri.receipt_id = r.id AND r.completed = 1
        GROUP BY p.id, p.name, p.category, p.price, p.stock, p.image_url
        ORDER BY p.stock ASC
    ";
    
    $result = executeQuery($query);
    $inventory = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $inventory[] = $row;
    }
    return $inventory;
}

// Get inventory statistics
function getInventoryStats($conn) {
    $stats = [];
    
    // Total products
    $result = executeQuery("SELECT COUNT(*) as count FROM products");
    $stats['total_products'] = mysqli_fetch_assoc($result)['count'];
    
    // Low stock products (less than 10)
    $result = executeQuery("SELECT COUNT(*) as count FROM products WHERE stock < 10");
    $stats['low_stock'] = mysqli_fetch_assoc($result)['count'];
    
    // Out of stock products
    $result = executeQuery("SELECT COUNT(*) as count FROM products WHERE stock = 0");
    $stats['out_of_stock'] = mysqli_fetch_assoc($result)['count'];
    
    // Total inventory value
    $result = executeQuery("SELECT SUM(price * stock) as total_value FROM products");
    $stats['total_value'] = mysqli_fetch_assoc($result)['total_value'] ?? 0;
    
    return $stats;
}

$inventory = getInventoryData($conn);
$stats = getInventoryStats($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Legend Brews Admin</title>
    
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
        
        .stat-card.danger {
            border-left-color: #dc3545;
        }
        
        .stat-card.warning {
            border-left-color: #ffc107;
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
        
        .stat-card.danger .stat-icon {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .stat-card.warning .stat-icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
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
        
        /* Inventory Table */
        .inventory-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .inventory-header {
            background: var(--coffee-dark);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .inventory-title {
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
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--coffee-dark);
        }
        
        .product-category {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .stock-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .stock-badge.in-stock {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-badge.low-stock {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-badge.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        
        .stock-input {
            width: 80px;
            padding: 0.3rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
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
        
        .btn-update {
            background: #28a745;
            color: white;
        }
        
        .btn-update:hover {
            background: #218838;
        }
        
        .btn-restock {
            background: #17a2b8;
            color: white;
        }
        
        .btn-restock:hover {
            background: #138496;
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
                <a href="inventory.php" class="active">
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
            <h1 class="mb-3">Inventory Management</h1>
            <p class="text-muted">Monitor and manage your product inventory levels</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_products']); ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['low_stock']); ?></div>
                <div class="stat-label">Low Stock Items</div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['out_of_stock']); ?></div>
                <div class="stat-label">Out of Stock</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_value'], 2); ?></div>
                <div class="stat-label">Total Inventory Value</div>
            </div>
        </div>
        
        <!-- Inventory Table -->
        <div class="inventory-container">
            <div class="inventory-header">
                <h3 class="inventory-title">Product Inventory</h3>
                <div class="filter-buttons">
                    <button class="filter-btn active" onclick="filterInventory('all')">All Items</button>
                    <button class="filter-btn" onclick="filterInventory('low')">Low Stock</button>
                    <button class="filter-btn" onclick="filterInventory('out')">Out of Stock</button>
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Price</th>
                        <th>Value</th>
                        <th>Sold</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <?php if (empty($inventory)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No inventory data found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inventory as $item): ?>
                            <tr data-stock-status="<?php 
                                if ($item['stock'] == 0) echo 'out';
                                elseif ($item['stock'] < 10) echo 'low';
                                else echo 'normal';
                            ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="product-image me-3"
                                             onerror="this.src='https://via.placeholder.com/50x50/6f4e37/ffffff?text=No+Image'">
                                        <div>
                                            <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <small class="text-muted">ID: <?php echo $item['id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="product-category <?php echo htmlspecialchars($item['category']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($item['category'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" class="stock-input" value="<?php echo $item['stock']; ?>" 
                                               id="stock-<?php echo $item['id']; ?>" min="0">
                                        <?php 
                                        if ($item['stock'] == 0) {
                                            echo '<span class="stock-badge out-of-stock">Out of Stock</span>';
                                        } elseif ($item['stock'] < 10) {
                                            echo '<span class="stock-badge low-stock">Low Stock</span>';
                                        } else {
                                            echo '<span class="stock-badge in-stock">In Stock</span>';
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <strong>₱<?php echo number_format($item['price'], 2); ?></strong>
                                </td>
                                <td>
                                    <strong>₱<?php echo number_format($item['price'] * $item['stock'], 2); ?></strong>
                                </td>
                                <td>
                                    <?php echo number_format($item['total_sold'] ?? 0); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-update" onclick="updateStock(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <button class="btn-action btn-restock" onclick="restockItem(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-plus"></i>
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
        // Filter inventory
        function filterInventory(status) {
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Filter table rows
            const rows = document.querySelectorAll('#inventoryTableBody tr');
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else if (status === 'low' && row.dataset.stockStatus === 'low') {
                    row.style.display = '';
                } else if (status === 'out' && row.dataset.stockStatus === 'out') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Update stock
        function updateStock(productId) {
            const stockInput = document.getElementById('stock-' + productId);
            const newStock = parseInt(stockInput.value);
            
            if (newStock < 0) {
                alert('Stock cannot be negative');
                return;
            }
            
            // Simulate API call
            alert('Stock updated to ' + newStock + ' for product ID ' + productId);
            
            // Update stock status badge
            const row = stockInput.closest('tr');
            const badge = row.querySelector('.stock-badge');
            
            if (newStock == 0) {
                badge.className = 'stock-badge out-of-stock';
                badge.textContent = 'Out of Stock';
                row.dataset.stockStatus = 'out';
            } else if (newStock < 10) {
                badge.className = 'stock-badge low-stock';
                badge.textContent = 'Low Stock';
                row.dataset.stockStatus = 'low';
            } else {
                badge.className = 'stock-badge in-stock';
                badge.textContent = 'In Stock';
                row.dataset.stockStatus = 'normal';
            }
        }
        
        // Restock item
        function restockItem(productId) {
            const stockInput = document.getElementById('stock-' + productId);
            const currentStock = parseInt(stockInput.value);
            const restockAmount = prompt('Enter restock quantity:', '50');
            
            if (restockAmount && !isNaN(restockAmount)) {
                const newStock = currentStock + parseInt(restockAmount);
                stockInput.value = newStock;
                updateStock(productId);
            }
        }
    </script>
</body>
</html>
