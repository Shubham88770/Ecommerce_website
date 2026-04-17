<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/db.php");
include("../includes/header.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($order_id == 0){
    echo "<div class='alert alert-danger'>Invalid Order</div>";
    include("../includes/footer.php");
    exit();
}

// FETCH ORDER
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id=$uid")->fetch_assoc();

if(!$order){
    echo "<div class='alert alert-danger'>Order not found</div>";
    include("../includes/footer.php");
    exit();
}

// FETCH ITEMS with product images
$items = $conn->query("
SELECT order_items.*, products.name, products.image, products.description 
FROM order_items 
JOIN products ON order_items.product_id = products.id 
WHERE order_id=$order_id
");

// FETCH ADDRESS
$address = $conn->query("SELECT * FROM addresses WHERE id={$order['address_id']}")->fetch_assoc();

// DELIVERY DATE
$delivery_date = date("d M Y", strtotime($order['created_at']." +3 days"));

// REVIEW SUBMIT
if(isset($_POST['submit_review'])){
    $rating = (int)$_POST['rating'];
    $review = $conn->real_escape_string($_POST['review']);
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if($product_id > 0){
        $check = $conn->query("SELECT id FROM reviews WHERE user_id=$uid AND product_id=$product_id");
        if($check->num_rows == 0){
            $conn->query("INSERT INTO reviews(user_id, product_id, rating, review) VALUES($uid, $product_id, '$rating', '$review')");
            echo "<script>alert('⭐ Review added successfully!'); window.location.href='order_details.php?id=$order_id';</script>";
        } else {
            echo "<script>alert('You have already reviewed this product.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Order Details - #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f7fb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        /* Main container */
        .order-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Cards */
        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 600;
            font-size: 16px;
            background: white;
        }

        .card-body {
            padding: 20px;
        }

        /* Tracking Bar - Horizontal like image */
        .tracking-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .track-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 20px 0;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            background: #e4e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 18px;
            color: #8b8f94;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background: #4caf50;
            color: white;
        }

        .step.completed .step-circle {
            background: #4caf50;
            color: white;
        }

        .step-label {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
        }

        .step.active .step-label {
            color: #4caf50;
            font-weight: 600;
        }

        .track-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e4e7eb;
            z-index: 1;
        }

        /* Horizontal Product Card - Like your image */
        .product-horizontal {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .product-horizontal:hover {
            background: #fafafa;
        }

        .product-image-side {
            flex-shrink: 0;
            width: 120px;
            height: 120px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image-side img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-details-side {
            flex: 1;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #212529;
        }

        .product-meta {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #2e7d32;
        }

        .product-quantity {
            font-size: 13px;
            color: #495057;
            margin-top: 5px;
        }

        .seller-info {
            font-size: 12px;
            color: #878787;
            margin-top: 8px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-delivered {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-shipped {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-processing {
            background: #fff3e0;
            color: #e65100;
        }

        /* Address section */
        .address-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        /* Buttons */
        .btn-invoice {
            background: #2874f0;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-invoice:hover {
            background: #1e5ec7;
            color: white;
        }

        /* Rating stars */
        .star-rating {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }
        .star-rating i {
            font-size: 24px;
            cursor: pointer;
            color: #ddd;
            transition: 0.2s;
        }
        .star-rating i.selected, .star-rating i:hover {
            color: #ffc107;
        }

        @media (max-width: 768px) {
            .product-horizontal {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .product-image-side {
                width: 150px;
                height: 150px;
            }
            .track-steps::before {
                top: 20px;
            }
            .step-circle {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="order-container">
    
    <!-- Header with Order ID -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h4 class="mb-1">📦 Order Details</h4>
            <p class="text-muted mb-0">Order ID: <strong>#<?php echo str_pad($order_id, 8, '0', STR_PAD_LEFT); ?></strong></p>
        </div>
        <a href="orders.php" class="btn btn-outline-secondary btn-sm">
            ← Back to Orders
        </a>
    </div>

    <!-- TRACKING SECTION - Horizontal Timeline -->
    <div class="tracking-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">🚚 Delivery Status</h6>
            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                <?php echo $order['status']; ?>
            </span>
        </div>
        
        <div class="track-steps">
            <?php
            $status = $order['status'];
            $steps_list = ['Ordered', 'Processing', 'Shipped', 'Delivered'];
            $current_index = 0;
            if($status == 'Pending') $current_index = 0;
            elseif($status == 'Processing') $current_index = 1;
            elseif($status == 'Shipped') $current_index = 2;
            elseif($status == 'Delivered') $current_index = 3;
            
            foreach($steps_list as $idx => $step_name):
                $is_active = ($idx <= $current_index);
            ?>
            <div class="step <?php echo $is_active ? 'active' : ''; ?>">
                <div class="step-circle">
                    <?php 
                    if($step_name == 'Ordered') echo '✓';
                    elseif($step_name == 'Processing') echo '⚙';
                    elseif($step_name == 'Shipped') echo '📦';
                    else echo '🏠';
                    ?>
                </div>
                <div class="step-label"><?php echo $step_name; ?></div>
                <small class="text-muted" style="font-size: 10px;">
                    <?php 
                    if($step_name == 'Ordered') echo date('d M', strtotime($order['created_at']));
                    ?>
                </small>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-3 text-center text-muted small">
            <i class="far fa-calendar-alt"></i> Expected Delivery: <strong><?php echo $delivery_date; ?></strong> by 11 PM
        </div>
    </div>

    <!-- ORDER INFO + INVOICE -->
    <div class="order-card">
        <div class="card-header">
            📋 Order Summary
            <a href="invoice.php?id=<?php echo $order_id; ?>" class="float-end btn-invoice btn-sm">
                📄 Download Invoice
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total Amount:</strong> <span style="color:#2e7d32; font-size:20px; font-weight:700;">₹<?php echo number_format($order['total'], 2); ?></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTS - HORIZONTAL LAYOUT (Image + Details side by side) -->
    <div class="order-card">
        <div class="card-header">
            🛍️ Items in this order
        </div>
        <div class="card-body p-0">
            <?php 
            $product_count = 0;
            while($product = $items->fetch_assoc()): 
                $product_count++;
                // Fix product image path
                $image_path = "../assets/images/" . $product['image'];
                if(empty($product['image']) || !file_exists($image_path)) {
                    // Placeholder image if real image missing
                    $image_path = "https://via.placeholder.com/120x120?text=Product";
                }
            ?>
            <!-- HORIZONTAL PRODUCT CARD - Exactly like your reference image -->
            <div class="product-horizontal">
                <div class="product-image-side">
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         onerror="this.src='https://via.placeholder.com/120x120?text=No+Image'">
                </div>
                <div class="product-details-side">
                    <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-meta">
                        Size: Free, Brown
                    </div>
                    <div class="seller-info">
                        Seller: WS Retail
                    </div>
                    <div class="product-quantity">
                        Quantity: <?php echo $product['quantity']; ?>
                    </div>
                    <div class="product-price mt-2">
                        ₹<?php echo number_format($product['price'], 2); ?>
                    </div>
                </div>
                <div class="text-end" style="min-width: 100px;">
                    <div class="fw-bold">₹<?php echo number_format($product['price'] * $product['quantity'], 2); ?></div>
                    <small class="text-muted">Total</small>
                </div>
            </div>
            
            <!-- Review section for each product (if delivered) -->
            <?php if($order['status'] == 'Delivered'): 
                $pid = $product['product_id'];
                $review_check = $conn->query("SELECT id FROM reviews WHERE user_id=$uid AND product_id=$pid");
                $reviewed = $review_check->num_rows > 0;
            ?>
            <div class="px-4 pb-3" style="background: #fef9e6; margin: 0 20px 15px 20px; border-radius: 8px;">
                <?php if(!$reviewed): ?>
                <form method="post" class="mt-2">
                    <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                    <div class="fw-bold mb-2">⭐ Rate this product</div>
                    <div class="star-rating" data-pid="<?php echo $pid; ?>">
                        <i class="fas fa-star" data-value="1"></i>
                        <i class="fas fa-star" data-value="2"></i>
                        <i class="fas fa-star" data-value="3"></i>
                        <i class="fas fa-star" data-value="4"></i>
                        <i class="fas fa-star" data-value="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="rating_<?php echo $pid; ?>" value="5">
                    <textarea name="review" class="form-control form-control-sm mb-2" rows="2" placeholder="Write your review..."></textarea>
                    <button type="submit" name="submit_review" class="btn btn-success btn-sm">Submit Review</button>
                </form>
                <?php else: ?>
                <div class="text-success py-2"><i class="fas fa-check-circle"></i> You have already reviewed this product. Thank you!</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php endwhile; ?>
            
            <?php if($product_count == 0): ?>
                <div class="p-4 text-center text-muted">No products found.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- DELIVERY ADDRESS -->
    <div class="order-card">
        <div class="card-header">
            📍 Delivery Address
        </div>
        <div class="card-body">
            <?php if($address): ?>
            <div class="address-block">
                <div class="fw-bold"><?php echo htmlspecialchars($address['full_name']); ?></div>
                <div>📞 <?php echo htmlspecialchars($address['phone']); ?></div>
                <div><?php echo htmlspecialchars($address['address']); ?></div>
                <div><?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> - <?php echo htmlspecialchars($address['pincode']); ?></div>
            </div>
            <?php else: ?>
            <p class="text-muted">Address not available</p>
            <?php endif; ?>
            
            <hr>
            <div class="small text-muted">
                <i class="fas fa-map-marker-alt"></i> Help our delivery agent reach you faster.
                <button class="btn btn-link btn-sm p-0 ms-1" onclick="alert('📍 Location sharing feature coming soon')">Share Location</button>
            </div>
        </div>
    </div>

    <!-- Chat Support -->
    <div class="order-card">
        <div class="card-header">
            💬 Support
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <span><i class="far fa-envelope"></i> support@parkavenue.com</span>
                <button class="btn btn-outline-primary btn-sm" onclick="alert('Chat with support')">
                    <i class="fab fa-whatsapp"></i> Chat with us
                </button>
            </div>
        </div>
    </div>
    
    <div class="text-center text-muted small mt-3">
        Invoice can be downloaded after 24 hours of delivery.
    </div>
</div>

<script>
    // Star rating functionality
    document.querySelectorAll('.star-rating').forEach(ratingDiv => {
        const stars = ratingDiv.querySelectorAll('.fas.fa-star');
        const hiddenInput = ratingDiv.nextElementSibling;
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.getAttribute('data-value'));
                if(hiddenInput) hiddenInput.value = value;
                
                stars.forEach(s => {
                    const starVal = parseInt(s.getAttribute('data-value'));
                    if(starVal <= value) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });
            
            // default select 5 stars
            const starVal = parseInt(star.getAttribute('data-value'));
            if(starVal <= 5) {
                star.classList.add('selected');
            }
        });
    });
</script>

<?php include("../includes/footer.php"); ?>
</body>
</html>