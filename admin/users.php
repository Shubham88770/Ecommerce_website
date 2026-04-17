<?php
include("../config/db.php");
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Get all users with role='user'
$result = $conn->query("SELECT * FROM users WHERE role='user' ORDER BY id DESC");

// Get statistics
$total_users = $result->num_rows;
$new_users_today = 0;
$active_users = $total_users;

// Get today's new users (if created_at column exists)
if($conn->query("SHOW COLUMNS FROM users LIKE 'created_at'")->num_rows > 0) {
    $today = date('Y-m-d');
    $today_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user' AND DATE(created_at) = '$today'");
    if($today_result && $row = $today_result->fetch_assoc()) {
        $new_users_today = $row['total'];
    }
}

// Get users who placed orders
$ordered_users = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM orders")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin Panel</title>
    
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
        .users-container {
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

        /* Users Table Card */
        .users-table-card {
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

        .search-box {
            display: flex;
            gap: 0.5rem;
        }

        .search-box input {
            border: 2px solid #e2e8f0;
            border-radius: 2rem;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            width: 250px;
        }

        .search-box button {
            border-radius: 2rem;
            padding: 0.4rem 1rem;
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

        /* User Avatar */
        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #f97316, #ef4444);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 1rem;
        }

        .user-email {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Badge */
        .badge-custom {
            background: #dcfce7;
            color: #166534;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-ordered {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-view-orders {
            background: #e2e8f0;
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }

        .btn-view-orders:hover {
            background: #f97316;
            color: white;
        }

        .btn-block-user {
            background: #fee2e2;
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #ef4444;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-block-user:hover {
            background: #ef4444;
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
        @media (max-width: 768px) {
            .users-container {
                padding: 1rem;
            }
            .table-header {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                width: 100%;
            }
            .search-box input {
                flex: 1;
                width: auto;
            }
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            .user-name {
                font-size: 0.85rem;
            }
            .user-email {
                font-size: 0.65rem;
            }
            .btn-view-orders, .btn-block-user {
                padding: 0.2rem 0.6rem;
                font-size: 0.65rem;
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
                    <i class="fas fa-users"></i> Manage Users
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
                <a href="orders.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-truck me-1"></i> Orders
                </a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="users-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-users text-warning me-2"></i> Customer Management</h1>
        <p>View and manage all registered customers</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe;">
                <i class="fas fa-users" style="color: #2563eb;"></i>
            </div>
            <h3><?php echo $total_users; ?></h3>
            <p>Total Customers</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #dcfce7;">
                <i class="fas fa-user-plus" style="color: #16a34a;"></i>
            </div>
            <h3><?php echo $new_users_today; ?></h3>
            <p>New Today</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">
                <i class="fas fa-shopping-cart" style="color: #d97706;"></i>
            </div>
            <h3><?php echo $ordered_users; ?></h3>
            <p>Have Placed Orders</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fed7aa;">
                <i class="fas fa-chart-line" style="color: #ea580c;"></i>
            </div>
            <h3><?php echo $active_users; ?></h3>
            <p>Active Customers</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="users-table-card">
        <div class="table-header">
            <h3>
                <i class="fas fa-list text-warning"></i>
                All Customers
                <span class="badge bg-secondary rounded-pill ms-2"><?php echo $total_users; ?></span>
            </h3>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name or email..." class="form-control">
                <button class="btn btn-outline-secondary rounded-pill" onclick="searchUsers()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0): 
                        $counter = 1;
                        while($row = $result->fetch_assoc()):
                            // Get initials for avatar
                            $name_parts = explode(' ', $row['name']);
                            $initials = strtoupper(substr($name_parts[0], 0, 1));
                            if(isset($name_parts[1])) {
                                $initials .= strtoupper(substr($name_parts[1], 0, 1));
                            }
                            
                            // Get user's order count
                            $order_count = 0;
                            $order_result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE user_id = ".$row['id']);
                            if($order_result && $order_data = $order_result->fetch_assoc()) {
                                $order_count = $order_data['total'];
                            }
                            
                            // Get user's total spent
                            $total_spent = 0;
                            $spent_result = $conn->query("SELECT SUM(total) as total FROM orders WHERE user_id = ".$row['id']." AND status='Delivered'");
                            if($spent_result && $spent_data = $spent_result->fetch_assoc()) {
                                $total_spent = $spent_data['total'] ?: 0;
                            }
                            
                            // Get join date
                            $join_date = isset($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : 'N/A';
                        ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?php echo $counter++; ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo $initials; ?>
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <span class="user-email">ID: #<?php echo $row['id']; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-details">
                                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                                    <?php if($order_count > 0): ?>
                                        <span class="small text-muted">
                                            <i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($total_spent, 0); ?> spent
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if($order_count > 0): ?>
                                    <span class="badge-custom">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge-custom" style="background: #fef3c7; color: #92400e;">
                                        <i class="fas fa-user"></i> New
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-bold"><?php echo $order_count; ?></span>
                                <span class="text-muted small">orders</span>
                            </td>
                            <td class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo $join_date; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="user_orders.php?user_id=<?php echo $row['id']; ?>" class="btn-view-orders">
                                        <i class="fas fa-eye"></i> View Orders
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <h5>No Customers Found</h5>
                                    <p class="text-muted">No registered customers yet.</p>
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
    // Search functionality
    function searchUsers() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('usersTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const nameCell = tr[i].getElementsByTagName('td')[1];
            const emailCell = tr[i].getElementsByTagName('td')[2];
            if (nameCell && emailCell) {
                const nameValue = nameCell.textContent || nameCell.innerText;
                const emailValue = emailCell.textContent || emailCell.innerText;
                if (nameValue.toUpperCase().indexOf(filter) > -1 || emailValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }

    // Enter key search
    document.getElementById('searchInput')?.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            searchUsers();
        }
    });
</script>

</body>
</html>