<?php
include("../config/db.php");
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Get admin name if exists
$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Dashboard Stats
$totalProducts = $conn->query("SELECT * FROM products")->num_rows;
$totalOrders = $conn->query("SELECT * FROM orders")->num_rows;
$totalUsers = $conn->query("SELECT * FROM users WHERE role='user'")->num_rows;

// Get recent orders for quick view
$recentOrders = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");

// Get low stock products (if stock column exists)
$lowStock = 0;
$stockCheck = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if($stockCheck && $stockCheck->num_rows > 0) {
    $lowStockResult = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock < 10 AND stock > 0");
    if($lowStockResult) {
        $lowStock = $lowStockResult->fetch_assoc()['total'];
    }
}

// Get out of stock products
$outOfStock = 0;
if($stockCheck && $stockCheck->num_rows > 0) {
    $outResult = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock = 0");
    if($outResult) {
        $outOfStock = $outResult->fetch_assoc()['total'];
    }
}

// Get today's orders and revenue (if created_at exists)
$todayOrders = 0;
$todayRevenue = 0;
$ordersCheck = $conn->query("SHOW COLUMNS FROM orders LIKE 'created_at'");
if($ordersCheck && $ordersCheck->num_rows > 0) {
    $today = date('Y-m-d');
    $todayResult = $conn->query("SELECT COUNT(*) as total, SUM(total) as revenue FROM orders WHERE DATE(created_at) = '$today'");
    if($todayResult && $row = $todayResult->fetch_assoc()) {
        $todayOrders = $row['total'];
        $todayRevenue = $row['revenue'] ?: 0;
    }
}

// Get total revenue (all time)
$totalRevenue = 0;
$revenueResult = $conn->query("SELECT SUM(total) as total FROM orders");
if($revenueResult && $row = $revenueResult->fetch_assoc()) {
    $totalRevenue = $row['total'] ?: 0;
}

// Get pending orders count
$pendingOrders = 0;
$pendingResult = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending' OR status = 'Pending'");
if($pendingResult && $row = $pendingResult->fetch_assoc()) {
    $pendingOrders = $row['total'];
}

// Get this month's revenue
$thisMonthRevenue = 0;
if($ordersCheck && $ordersCheck->num_rows > 0) {
    $firstDay = date('Y-m-01');
    $lastDay = date('Y-m-t');
    $monthResult = $conn->query("SELECT SUM(total) as total FROM orders WHERE DATE(created_at) BETWEEN '$firstDay' AND '$lastDay'");
    if($monthResult && $row = $monthResult->fetch_assoc()) {
        $thisMonthRevenue = $row['total'] ?: 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Dashboard | MyShop Admin</title>
    
    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
        }

        .sidebar-header {
            padding: 1.8rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #f97316, #ef4444);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon i {
            font-size: 1.4rem;
            color: white;
        }

        .logo-text h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .logo-text p {
            font-size: 0.7rem;
            color: #94a3b8;
            margin: 0;
        }

        .nav-menu {
            padding: 1.8rem 1rem;
        }

        .nav-item {
            list-style: none;
            margin-bottom: 0.5rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.85rem 1.2rem;
            border-radius: 14px;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link-custom i {
            width: 24px;
            font-size: 1.2rem;
        }

        .nav-link-custom:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            transform: translateX(4px);
        }

        .nav-link-custom.active {
            background: linear-gradient(135deg, #f97316, #ef4444);
            color: white;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #f97316, #ef4444);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
        }

        .admin-details h5 {
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .admin-details p {
            font-size: 0.7rem;
            color: #94a3b8;
            margin: 0;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: 280px;
            padding: 1.8rem 2rem;
            min-height: 100vh;
        }

        /* Top Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .page-title p {
            color: #64748b;
            margin: 0;
            font-size: 0.85rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            transition: all 0.3s;
            border: 1px solid #eef2ff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.1);
            border-color: #fed7aa;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 1.6rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0.5rem 0 0.2rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 0.2rem 0.6rem;
            border-radius: 1rem;
            background: #f1f5f9;
        }

        /* Charts Row */
        .charts-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #eef2ff;
        }

        .chart-title {
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #334155;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #eef2ff;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.7rem 1.5rem;
            border-radius: 2rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #f97316, #ef4444);
            color: white;
            border: none;
        }

        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
            color: white;
        }

        .btn-outline-action {
            background: white;
            border: 1px solid #e2e8f0;
            color: #475569;
        }

        .btn-outline-action:hover {
            background: #f8fafc;
            border-color: #f97316;
            color: #f97316;
        }

        /* Recent Orders Table */
        .recent-orders {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #eef2ff;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
        }

        .table-custom tbody tr {
            transition: all 0.2s;
        }

        .table-custom tbody tr:hover {
            background: #fef9f0;
        }

        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-delivered {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-sm-view {
            background: #f1f5f9;
            border: none;
            padding: 0.3rem 0.9rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            color: #475569;
            transition: all 0.2s;
        }

        .btn-sm-view:hover {
            background: #f97316;
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            .stat-value {
                font-size: 1.5rem;
            }
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: white;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            border-radius: 0.8rem;
            color: #475569;
        }
        
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-store"></i>
            </div>
            <div class="logo-text">
                <h3>SwiftCart</h3>
                <p>Admin Dashboard</p>
            </div>
        </div>
    </div>
    
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link-custom active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="products.php" class="nav-link-custom">
                <i class="fas fa-box-open"></i>
                <span>Products</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="orders.php" class="nav-link-custom">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
                <?php if($pendingOrders > 0): ?>
                <span class="badge bg-warning text-dark ms-auto rounded-pill"><?php echo $pendingOrders; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="users.php" class="nav-link-custom">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="add_product.php" class="nav-link-custom">
                <i class="fas fa-plus-circle"></i>
                <span>Add Product</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="settings.php" class="nav-link-custom">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
            <div class="admin-details">
                <h5><?php echo htmlspecialchars($adminName); ?></h5>
                <p>Administrator</p>
            </div>
            <a href="logout.php" class="text-decoration-none ms-auto" style="color: #94a3b8;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Header with Mobile Menu -->
    <div class="top-header">
        <div class="page-title">
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i> Menu
            </button>
            <h1>Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($adminName); ?>! Here's your store overview.</p>
        </div>
        <div class="header-actions">
            <div class="date-badge">
                <i class="fas fa-calendar-alt"></i>
                <?php echo date('l, d M Y'); ?>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe);">
                    <i class="fas fa-box" style="color: #2563eb;"></i>
                </div>
                <?php if($lowStock > 0): ?>
                <span class="stat-trend text-warning">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $lowStock; ?> low stock
                </span>
                <?php endif; ?>
            </div>
            <div class="stat-value"><?php echo $totalProducts; ?></div>
            <div class="stat-label">Total Products</div>
            <?php if($outOfStock > 0): ?>
            <small class="text-danger"><?php echo $outOfStock; ?> out of stock</small>
            <?php endif; ?>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #dcfce7, #bbf7d0);">
                    <i class="fas fa-shopping-bag" style="color: #16a34a;"></i>
                </div>
                <?php if($pendingOrders > 0): ?>
                <span class="stat-trend text-warning">
                    <i class="fas fa-clock"></i> <?php echo $pendingOrders; ?> pending
                </span>
                <?php endif; ?>
            </div>
            <div class="stat-value"><?php echo $totalOrders; ?></div>
            <div class="stat-label">Total Orders</div>
            <?php if($todayOrders > 0): ?>
            <small class="text-success">+<?php echo $todayOrders; ?> today</small>
            <?php endif; ?>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fed7aa, #fdba74);">
                    <i class="fas fa-users" style="color: #ea580c;"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $totalUsers; ?></div>
            <div class="stat-label">Active Customers</div>
            <small class="text-muted">Registered users</small>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fef9c3, #fef08a);">
                    <i class="fas fa-rupee-sign" style="color: #ca8a04;"></i>
                </div>
            </div>
            <div class="stat-value">₹<?php echo number_format($totalRevenue, 0); ?></div>
            <div class="stat-label">Total Revenue</div>
            <?php if($thisMonthRevenue > 0): ?>
            <small class="text-success">₹<?php echo number_format($thisMonthRevenue, 0); ?> this month</small>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="charts-row">
        <div class="chart-card">
            <div class="chart-title">
                <i class="fas fa-chart-line" style="color: #f97316;"></i>
                <span>Weekly Orders</span>
            </div>
            <canvas id="weeklyOrdersChart" height="200"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">
                <i class="fas fa-chart-pie" style="color: #ef4444;"></i>
                <span>Order Status Distribution</span>
            </div>
            <canvas id="statusPieChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="section-title">
            <i class="fas fa-bolt text-warning"></i>
            <span>Quick Actions</span>
        </div>
        <div class="action-buttons">
            <a href="add_product.php" class="btn-action btn-primary-action">
                <i class="fas fa-plus-circle"></i> Add New Product
            </a>
            <a href="products.php" class="btn-action btn-outline-action">
                <i class="fas fa-edit"></i> Manage Products
            </a>
            <a href="orders.php" class="btn-action btn-outline-action">
                <i class="fas fa-truck"></i> View All Orders
            </a>
            <a href="users.php" class="btn-action btn-outline-action">
                <i class="fas fa-user-plus"></i> Manage Customers
            </a>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <?php if($recentOrders && $recentOrders->num_rows > 0): ?>
    <div class="recent-orders">
        <div class="section-title">
            <i class="fas fa-clock"></i>
            <span>Recent Orders</span>
            <a href="orders.php" class="ms-auto text-decoration-none small" style="color: #f97316;">View All →</a>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
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
                    <?php while($order = $recentOrders->fetch_assoc()): 
                        $status = $order['status'] ?? 'Pending';
                        $statusClass = '';
                        if(stripos($status, 'delivered') !== false) $statusClass = 'status-delivered';
                        elseif(stripos($status, 'pending') !== false) $statusClass = 'status-pending';
                        elseif(stripos($status, 'processing') !== false) $statusClass = 'status-processing';
                        else $statusClass = 'status-pending';
                    ?>
                    <tr>
                        <td><span class="fw-bold">#<?php echo $order['id']; ?></span></td>
                        <td>Customer #<?php echo $order['user_id']; ?></td>
                        <td class="fw-bold text-success">₹<?php echo number_format($order['total'], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <i class="fas <?php echo stripos($status, 'delivered') !== false ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if(isset($order['created_at']) && $order['created_at']) {
                                echo date('d M Y, h:i A', strtotime($order['created_at']));
                            } else {
                                echo date('d M Y');
                            }
                            ?>
                        </td>
                        <td>
                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-sm-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile menu toggle
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    
    if(mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if(window.innerWidth <= 992) {
            if(!sidebar.contains(event.target) && !mobileBtn.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
    
    // Weekly Orders Chart (simulated data - you can replace with actual data from DB)
    const weeklyCtx = document.getElementById('weeklyOrdersChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Orders',
                data: [12, 19, 15, 17, 24, 28, 22],
                borderColor: '#f97316',
                backgroundColor: 'rgba(249, 115, 22, 0.05)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f97316',
                pointBorderColor: 'white',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#eef2ff'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Status Pie Chart (actual data from PHP)
    <?php
    // Get actual order status counts for pie chart
    $statusCounts = ['Pending' => 0, 'Processing' => 0, 'Shipped' => 0, 'Delivered' => 0];
    $statusResult = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    if($statusResult) {
        while($row = $statusResult->fetch_assoc()) {
            $statusKey = ucfirst(strtolower($row['status']));
            if(isset($statusCounts[$statusKey])) {
                $statusCounts[$statusKey] = $row['count'];
            } else {
                $statusCounts['Pending'] += $row['count'];
            }
        }
    }
    ?>
    
    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Shipped', 'Delivered'],
            datasets: [{
                data: [<?php echo $statusCounts['Pending']; ?>, <?php echo $statusCounts['Processing']; ?>, <?php echo $statusCounts['Shipped']; ?>, <?php echo $statusCounts['Delivered']; ?>],
                backgroundColor: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981'],
                borderWidth: 0,
                cutout: '65%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
</script>

<style>
    .date-badge {
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: #475569;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .text-warning {
        color: #f97316 !important;
    }
    .text-success {
        color: #10b981 !important;
    }
    .badge.bg-warning {
        background-color: #f97316 !important;
    }
</style>

</body>
</html>