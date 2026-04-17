<?php
include("../config/db.php");
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

if(isset($_POST['update'])){
    $id = (int)$_POST['id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $conn->query("UPDATE orders SET status='$status' WHERE id=$id");
    
    if($conn->affected_rows > 0){
        $success_message = "Order #$id status updated to $status successfully!";
    } else {
        $error_message = "Failed to update order status.";
    }
}

// Get orders with user details
$result = $conn->query("SELECT orders.*, users.name as user_name, users.email as user_email 
                        FROM orders 
                        LEFT JOIN users ON orders.user_id = users.id 
                        ORDER BY orders.id DESC");

// Get statistics
$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status='Pending'")->fetch_assoc()['total'];
$delivered_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status='Delivered'")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(total) as total FROM orders WHERE status='Delivered'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin Panel</title>
    
    <!-- Bootstrap 5 + Font Awesome + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: #0f172a;
        }

        /* Premium Navbar */
        .navbar-premium {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #f97316, #ef4444);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .navbar-brand i {
            background: none;
            color: #f97316;
        }

        .admin-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            color: #fbbf24;
        }

        /* Container */
        .orders-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
        }

        .page-header p {
            color: #64748b;
            margin: 0;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1.2rem;
            padding: 1.2rem;
            transition: all 0.3s;
            border: 1px solid #eef2ff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }

        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #f97316, #ef4444);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.8rem;
        }

        .stat-icon i {
            font-size: 1.5rem;
        }

        .stat-card h3 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0.3rem 0 0;
        }

        .stat-card p {
            color: #64748b;
            font-size: 0.8rem;
            margin: 0;
            font-weight: 500;
        }

        /* Alert Messages */
        .alert-custom {
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            animation: slideDown 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success-custom {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .alert-danger-custom {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* Orders Table Card */
        .orders-table-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2ff;
        }

        .table-header {
            padding: 1.2rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .filter-select {
            border: 2px solid #e2e8f0;
            border-radius: 2rem;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            background: white;
        }

        .search-box {
            display: flex;
            gap: 0.5rem;
        }

        .search-box input {
            border: 2px solid #e2e8f0;
            border-radius: 2rem;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            width: 200px;
        }

        /* Table Styles */
        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: #f8fafc;
            padding: 1rem;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-custom tbody tr {
            transition: all 0.2s;
        }

        .table-custom tbody tr:hover {
            background: #fef3c7;
        }

        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
        }

        .order-id {
            font-weight: 700;
            color: #f97316;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #0f172a;
        }

        .user-email {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .order-total {
            font-weight: 800;
            color: #f97316;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-shipped {
            background: #cffafe;
            color: #0e7490;
        }

        .status-delivered {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Update Form */
        .update-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .status-select {
            border: 2px solid #e2e8f0;
            border-radius: 2rem;
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: white;
            transition: all 0.2s;
        }

        .status-select:focus {
            border-color: #f97316;
            outline: none;
        }

        .btn-update {
            background: linear-gradient(135deg, #f97316, #ea580c);
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
            transition: all 0.2s;
        }

        .btn-update:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
        }

        .btn-view {
            background: #e2e8f0;
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-view:hover {
            background: #f97316;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .orders-container {
                padding: 1rem;
            }
            .table-header {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group, .search-box {
                width: 100%;
            }
            .search-box input {
                flex: 1;
            }
            .update-form {
                flex-direction: column;
            }
            .status-select {
                width: 100%;
            }
            .btn-update {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Premium Navbar -->
<nav class="navbar-premium">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="navbar-brand">
                    <i class="fas fa-truck"></i> Manage Orders
                </span>
                <span class="admin-badge ms-2">
                    <i class="fas fa-shield-alt"></i> Admin Panel
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-chart-line me-1"></i> Dashboard
                </a>
                <a href="products.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-box-open me-1"></i> Products
                </a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="orders-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-truck text-warning me-2"></i> Order Management</h1>
        <p>View, track, and update customer orders</p>
    </div>

    <!-- Alert Messages -->
    <?php if($success_message): ?>
        <div class="alert-custom alert-success-custom">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $success_message; ?></span>
        </div>
    <?php endif; ?>
    
    <?php if($error_message): ?>
        <div class="alert-custom alert-danger-custom">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $error_message; ?></span>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe;">
                <i class="fas fa-shopping-cart" style="color: #2563eb;"></i>
            </div>
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">
                <i class="fas fa-clock" style="color: #d97706;"></i>
            </div>
            <h3><?php echo $pending_orders; ?></h3>
            <p>Pending Orders</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #dcfce7;">
                <i class="fas fa-check-circle" style="color: #16a34a;"></i>
            </div>
            <h3><?php echo $delivered_orders; ?></h3>
            <p>Delivered Orders</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fed7aa;">
                <i class="fas fa-rupee-sign" style="color: #ea580c;"></i>
            </div>
            <h3>₹<?php echo number_format($total_revenue ?: 0, 0); ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="orders-table-card">
        <div class="table-header">
            <h3>
                <i class="fas fa-list text-warning"></i>
                All Orders
                <span class="badge bg-secondary rounded-pill ms-2"><?php echo $total_orders; ?></span>
            </h3>
            <div class="d-flex gap-2">
                <div class="filter-group">
                    <select id="statusFilter" class="filter-select" onchange="filterByStatus()">
                        <option value="all">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by order ID or customer..." class="form-control">
                    <button class="btn btn-outline-secondary rounded-pill" onclick="searchOrders()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            $status = $row['status'];
                            $statusClass = '';
                            if($status == 'Pending') $statusClass = 'status-pending';
                            elseif($status == 'Processing') $statusClass = 'status-processing';
                            elseif($status == 'Shipped') $statusClass = 'status-shipped';
                            elseif($status == 'Delivered') $statusClass = 'status-delivered';
                            elseif($status == 'Cancelled') $statusClass = 'status-cancelled';
                            else $statusClass = 'status-pending';
                        ?>
                        <tr data-status="<?php echo $status; ?>">
                            <td>
                                <span class="order-id">#<?php echo $row['id']; ?></span>
                            </td>
                            <td>
                                <div class="user-info">
                                    <span class="user-name">
                                        <i class="fas fa-user-circle me-1 text-muted"></i>
                                        <?php echo htmlspecialchars($row['user_name'] ?? 'User #'.$row['user_id']); ?>
                                    </span>
                                    <span class="user-email"><?php echo htmlspecialchars($row['user_email'] ?? ''); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="order-total">₹<?php echo number_format($row['total'], 2); ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <i class="fas <?php 
                                        echo $status == 'Pending' ? 'fa-clock' : 
                                            ($status == 'Delivered' ? 'fa-check-circle' : 
                                            ($status == 'Cancelled' ? 'fa-times-circle' : 'fa-truck')); 
                                    ?>"></i>
                                    <?php echo $status; ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php 
                                if(isset($row['created_at'])) {
                                    echo date('d M Y, h:i A', strtotime($row['created_at']));
                                } else {
                                    echo date('d M Y');
                                }
                                ?>
                            </td>
                            <td>
                                <div class="update-form">
                                    <form method="post" class="update-form" style="display: flex; gap: 0.5rem;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <select name="status" class="status-select">
                                            <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Processing" <?php echo $status == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo $status == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo $status == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo $status == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button name="update" class="btn-update">
                                            <i class="fas fa-sync-alt"></i> Update
                                        </button>
                                    </form>
                                    <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>No Orders Found</h5>
                                    <p class="text-muted">No orders have been placed yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-custom');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);

    // Filter by status
    function filterByStatus() {
        const filter = document.getElementById('statusFilter').value;
        const table = document.getElementById('ordersTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const statusCell = tr[i].getAttribute('data-status');
            if (filter === 'all' || statusCell === filter) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }

    // Search functionality
    function searchOrders() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('ordersTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const orderId = tr[i].getElementsByTagName('td')[0]?.innerText || '';
            const customerName = tr[i].getElementsByTagName('td')[1]?.innerText || '';
            if (orderId.toUpperCase().indexOf(filter) > -1 || customerName.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }

    // Enter key search
    document.getElementById('searchInput')?.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            searchOrders();
        }
    });
</script>

</body>
</html>