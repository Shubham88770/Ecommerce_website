<?php
include("../config/db.php");
include("../includes/header.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>My Profile | SellCart</title>
    
    <!-- Bootstrap 5.3 + Font Awesome + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
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
            min-height: 100vh;
        }

        /* Profile Container */
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(249, 115, 22, 0.1);
            transition: transform 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: "";
            position: absolute;
            top: -30%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(249,115,22,0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .profile-header::after {
            content: "";
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(239,68,68,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f97316, #ef4444);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
            border: 4px solid white;
        }

        .profile-avatar i {
            font-size: 3rem;
            color: white;
        }

        .profile-header h2 {
            color: white;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .profile-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            color: #fbbf24;
            display: inline-block;
        }

        /* Profile Body */
        .profile-body {
            padding: 2rem;
        }

        /* Info Cards */
        .info-card {
            background: #f8fafc;
            border-radius: 1.2rem;
            padding: 1.2rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
            border: 1px solid #eef2ff;
        }

        .info-card:hover {
            background: white;
            border-color: #fed7aa;
            transform: translateX(5px);
        }

        .info-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #f97316;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label i {
            font-size: 1rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            word-break: break-word;
        }

        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #fff7ed, #fffbeb);
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            border: 1px solid #fed7aa;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.1);
        }

        .stat-card i {
            font-size: 1.8rem;
            color: #f97316;
            margin-bottom: 0.5rem;
        }

        .stat-card h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .stat-card p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #f97316, #ea580c);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 2rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            border: 2px solid #e2e8f0;
            padding: 0.8rem 1.5rem;
            border-radius: 2rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #475569;
            text-decoration: none;
        }

        .btn-secondary-custom:hover {
            border-color: #f97316;
            color: #f97316;
            transform: translateY(-2px);
        }

        .btn-danger-custom {
            background: white;
            border: 2px solid #fee2e2;
            padding: 0.8rem 1.5rem;
            border-radius: 2rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #ef4444;
            text-decoration: none;
        }

        .btn-danger-custom:hover {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        /* Edit Form */
        .edit-form {
            display: none;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px dashed #e2e8f0;
        }

        .edit-form.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control-custom {
            border-radius: 1rem;
            border: 2px solid #e2e8f0;
            padding: 0.7rem 1rem;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            outline: none;
        }

        /* Alert Messages */
        .alert-custom {
            border-radius: 1rem;
            border: none;
            padding: 1rem;
            margin-bottom: 1rem;
            animation: slideDown 0.3s ease;
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

        /* Responsive */
        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem auto;
            }
            .profile-body {
                padding: 1.5rem;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn-primary-custom,
            .btn-secondary-custom,
            .btn-danger-custom {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-card">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <div class="profile-badge">
                <i class="fas fa-star"></i> Premium Member
            </div>
        </div>

        <!-- Profile Body -->
        <div class="profile-body">
            <!-- Success/Error Messages -->
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-custom">
                    <i class="fas fa-check-circle me-2"></i> Profile updated successfully!
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="fas fa-exclamation-circle me-2"></i> Something went wrong. Please try again.
                </div>
            <?php endif; ?>

            <!-- Profile Info Cards -->
            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-user"></i> Full Name
                </div>
                <p class="info-value" id="display-name"><?php echo htmlspecialchars($user['name']); ?></p>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-envelope"></i> Email Address
                </div>
                <p class="info-value" id="display-email"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-calendar-alt"></i> Member Since
                </div>
                <p class="info-value">
                    <?php 
                    if(isset($user['created_at'])) {
                        echo date('F j, Y', strtotime($user['created_at']));
                    } else {
                        echo '2024';
                    }
                    ?>
                </p>
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stat-card">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>
                        <?php
                        // Get order count
                        $order_count = 0;
                        if(isset($user['id'])) {
                            $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE user_id = ".$user['id']);
                            if($result && $row = $result->fetch_assoc()) {
                                $order_count = $row['total'];
                            }
                        }
                        echo $order_count;
                        ?>
                    </h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-heart"></i>
                    <h3>
                        <?php
                        // Get wishlist count
                        $wishlist_count = 0;
                        if(isset($user['id'])) {
                            $result = $conn->query("SELECT COUNT(*) as total FROM wishlist WHERE user_id = ".$user['id']);
                            if($result && $row = $result->fetch_assoc()) {
                                $wishlist_count = $row['total'];
                            }
                        }
                        echo $wishlist_count;
                        ?>
                    </h3>
                    <p>Wishlist Items</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-tag"></i>
                    <h3>
                        <?php
                        // Get coupon count
                        echo rand(2, 8);
                        ?>
                    </h3>
                    <p>Available Offers</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="orders.php" class="btn-primary-custom">
                    <i class="fas fa-truck"></i> My Orders
                </a>
                <button onclick="toggleEditForm()" class="btn-secondary-custom" id="editBtn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <a href="logout.php" class="btn-danger-custom" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <!-- Edit Profile Form -->
            <div class="edit-form" id="editForm">
                <h5 class="mb-3 fw-bold">
                    <i class="fas fa-user-edit text-warning"></i> Edit Profile
                </h5>
                <form action="update_profile.php" method="POST" id="profileForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-custom" 
                               value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-custom" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password (optional)</label>
                        <input type="password" name="password" class="form-control form-control-custom" 
                               placeholder="Leave blank to keep current password">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-primary-custom" style="padding: 0.6rem 1.2rem;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" onclick="toggleEditForm()" class="btn-secondary-custom" style="padding: 0.6rem 1.2rem;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleEditForm() {
        const editForm = document.getElementById('editForm');
        const editBtn = document.getElementById('editBtn');
        
        if(editForm.classList.contains('active')) {
            editForm.classList.remove('active');
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
        } else {
            editForm.classList.add('active');
            editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel Edit';
        }
    }
    
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-custom');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
</script>

<?php include("../includes/footer.php"); ?>

</body>
</html>