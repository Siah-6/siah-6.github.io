<?php
session_start();
require_once '../connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle CRUD operations
$message = '';
$messageType = '';

// Add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $image_url = trim($_POST['image_url']);
    $stock = intval($_POST['stock'] ?? 0);
    
    // Validation
    if (empty($name) || empty($description) || $price <= 0 || empty($category)) {
        $message = 'Please fill in all required fields with valid values';
        $messageType = 'danger';
    } else {
        $query = "INSERT INTO products (name, description, price, category, image_url, stock, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssdssi", $name, $description, $price, $category, $image_url, $stock);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Product added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error adding product: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }
}

// Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $image_url = trim($_POST['image_url']);
    $stock = intval($_POST['stock'] ?? 0);
    
    // Validation
    if (empty($name) || empty($description) || $price <= 0 || empty($category) || $id <= 0) {
        $message = 'Please fill in all required fields with valid values';
        $messageType = 'danger';
    } else {
        $query = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_url = ?, stock = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssdssii", $name, $description, $price, $category, $image_url, $stock, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Product updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating product: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }
}

// Delete product
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if ($id > 0) {
        // Check if product has orders
        $checkQuery = "SELECT COUNT(*) as count FROM receipt_items WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_assoc($result)['count'];
        
        if ($count > 0) {
            $message = 'Cannot delete product: It has existing orders';
            $messageType = 'danger';
        } else {
            $query = "DELETE FROM products WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Product deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error deleting product: ' . mysqli_error($conn);
                $messageType = 'danger';
            }
        }
    }
}

// Get all products
function getAllProducts($conn) {
    $query = "SELECT * FROM products ORDER BY created_at DESC";
    $result = executeQuery($query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

// Get product by ID
function getProductById($conn, $id) {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

$products = getAllProducts($conn);
$editingProduct = null;

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editingProduct = getProductById($conn, intval($_GET['id']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Legend Brews Admin</title>
    
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
        
        /* Header */
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin: 0;
        }
        
        /* Product Form */
        .product-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--coffee-medium);
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
            outline: none;
        }
        
        /* Products Table */
        .products-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .table-header {
            background: var(--coffee-dark);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .table-title {
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
        
        .product-image {
            width: 60px;
            height: 60px;
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
        
        .product-category.hot {
            background: #ffe6e6;
            color: #dc3545;
        }
        
        .product-category.cold {
            background: #e6f3ff;
            color: #0066cc;
        }
        
        .product-category.pastries {
            background: #fff3e6;
            color: #ff8800;
        }
        
        .product-category.merch {
            background: #e6ffe6;
            color: #28a745;
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
        
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        
        .btn-edit:hover {
            background: #138496;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--coffee-dark), var(--coffee-medium));
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 78, 55, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 2rem;
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
            
            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .search-box {
                width: 100%;
            }
            
            .data-table {
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
                <a href="products.php" class="active">
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
            <div>
                <h1 class="page-title">Product Management</h1>
                <p class="text-muted mb-0">Manage your coffee shop inventory</p>
            </div>
            <button class="btn btn-primary ms-3" onclick="toggleForm()">
                <i class="fas fa-plus me-2"></i>
                Add New Product
            </button>
        </div>

        <!-- Alert Message -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Product Form -->
        <div class="product-form" id="productForm" style="display: <?php echo $editingProduct ? 'block' : 'none'; ?>;">
            <h3 class="mb-4">
                <?php echo $editingProduct ? 'Edit Product' : 'Add New Product'; ?>
            </h3>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $editingProduct ? 'update' : 'add'; ?>">
                <?php if ($editingProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editingProduct['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($editingProduct['name'] ?? ''); ?>" 
                                   placeholder="Enter product name" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Select category</option>
                                <option value="hot" <?php echo ($editingProduct['category'] ?? '') === 'hot' ? 'selected' : ''; ?>>Hot Drinks</option>
                                <option value="cold" <?php echo ($editingProduct['category'] ?? '') === 'cold' ? 'selected' : ''; ?>>Cold Drinks</option>
                                <option value="pastries" <?php echo ($editingProduct['category'] ?? '') === 'pastries' ? 'selected' : ''; ?>>Pastries</option>
                                <option value="merch" <?php echo ($editingProduct['category'] ?? '') === 'merch' ? 'selected' : ''; ?>>Merchandise</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Enter product description" required><?php echo htmlspecialchars($editingProduct['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price" class="form-label">Price (₱) *</label>
                            <input type="number" class="form-control" id="price" name="price" 
                                   value="<?php echo htmlspecialchars($editingProduct['price'] ?? ''); ?>" 
                                   placeholder="0.00" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?php echo htmlspecialchars($editingProduct['stock'] ?? 0); ?>" 
                                   placeholder="0" min="0">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($editingProduct['image_url'] ?? ''); ?>" 
                                   placeholder="https://example.com/image.jpg">
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $editingProduct ? 'Update Product' : 'Add Product'; ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm()">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="products-table">
            <div class="table-header">
                <h3 class="table-title">Products Inventory</h3>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Search products..." id="searchInput">
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image"
                                     onerror="this.src='https://via.placeholder.com/60x60/6f4e37/ffffff?text=No+Image'">
                            </td>
                            <td>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></small>
                            </td>
                            <td>
                                <span class="product-category <?php echo htmlspecialchars($product['category']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($product['category'])); ?>
                                </span>
                            </td>
                            <td>
                                <strong>₱<?php echo number_format($product['price'], 2); ?></strong>
                            </td>
                            <td>
                                <?php 
                                $stock = $product['stock'] ?? 0;
                                if ($stock > 10) {
                                    echo '<span class="stock-badge in-stock">In Stock (' . $stock . ')</span>';
                                } elseif ($stock > 0) {
                                    echo '<span class="stock-badge low-stock">Low Stock (' . $stock . ')</span>';
                                } else {
                                    echo '<span class="stock-badge out-of-stock">Out of Stock</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="editProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle product form
        function toggleForm() {
            const form = document.getElementById('productForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            
            if (form.style.display === 'block') {
                // Reset form if adding new product
                if (!<?php echo $editingProduct ? 'true' : 'false'; ?>) {
                    document.querySelector('form').reset();
                }
            }
        }
        
        // Edit product
        function editProduct(id) {
            window.location.href = 'products.php?action=edit&id=' + id;
        }
        
        // Delete product
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                window.location.href = 'products.php?action=delete&id=' + id;
            }
        }
        
        // Search products
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTableBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const price = parseFloat(document.getElementById('price').value);
            const stock = parseInt(document.getElementById('stock').value);
            
            if (price <= 0) {
                e.preventDefault();
                alert('Price must be greater than 0');
                return false;
            }
            
            if (stock < 0) {
                e.preventDefault();
                alert('Stock cannot be negative');
                return false;
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
