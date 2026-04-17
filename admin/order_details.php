<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/db.php");

// 🔐 ADMIN CHECK
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// GET ORDER ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($order_id == 0){
    die("Invalid Order ID");
}

// FETCH ORDER + USER
$order = $conn->query("
SELECT orders.*, users.name AS user_name, users.email 
FROM orders 
JOIN users ON orders.user_id = users.id 
WHERE orders.id = $order_id
")->fetch_assoc();

if(!$order){
    die("Order not found");
}

// 🔥 UPDATE STATUS
if(isset($_POST['update_status'])){
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET status='$status' WHERE id=$order_id");

    echo "<script>alert('Status Updated'); window.location='order_details.php?id=$order_id';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f5f7fa; }
.card { border-radius:15px; }
</style>

</head>

<body>

<div class="container mt-5">

<h3 class="mb-4">📦 Order Details (#<?php echo $order_id; ?>)</h3>

<div class="card p-4 shadow-sm mb-4">

<!-- 🔥 USER INFO -->
<h5>👤 Customer Details</h5>
<p>
<b>Name:</b> <?php echo $order['user_name']; ?><br>
<b>Email:</b> <?php echo $order['email']; ?><br>
</p>

<hr>

<!-- 🔥 ORDER INFO -->
<h5>🧾 Order Info</h5>
<p>
<b>Total:</b> ₹<?php echo $order['total']; ?><br>
<b>Status:</b> 
<span class="badge bg-info"><?php echo $order['status']; ?></span><br>
<b>Date:</b> <?php echo $order['created_at']; ?>
</p>

<hr>

<!-- 🔥 STATUS UPDATE -->
<form method="post" class="mb-3">
<label>Update Status:</label>
<select name="status" class="form-control mb-2">

<option <?php if($order['status']=="Pending") echo "selected"; ?>>Pending</option>
<option <?php if($order['status']=="Confirmed") echo "selected"; ?>>Confirmed</option>
<option <?php if($order['status']=="Processing") echo "selected"; ?>>Processing</option>
<option <?php if($order['status']=="Shipped") echo "selected"; ?>>Shipped</option>
<option <?php if($order['status']=="Delivered") echo "selected"; ?>>Delivered</option>
<option <?php if($order['status']=="Cancelled") echo "selected"; ?>>Cancelled</option>

</select>

<button name="update_status" class="btn btn-success">Update</button>
</form>

</div>

<!-- 🔥 ORDER ITEMS -->
<div class="card p-4 shadow-sm mb-4">

<h5>🛒 Products</h5>

<?php
$items = $conn->query("
SELECT order_items.*, products.name, products.image 
FROM order_items 
JOIN products ON order_items.product_id = products.id 
WHERE order_id = $order_id
");

while($item = $items->fetch_assoc()):
?>

<div class="row mb-3 align-items-center border-bottom pb-2">

<div class="col-md-2">
<img src="../assets/images/<?php echo $item['image']; ?>" width="70">
</div>

<div class="col-md-6">
<?php echo $item['name']; ?><br>
Qty: <?php echo $item['quantity']; ?>
</div>

<div class="col-md-4 text-end">
₹<?php echo $item['price']; ?>
</div>

</div>

<?php endwhile; ?>

</div>

<!-- 🔥 ADDRESS -->
<div class="card p-4 shadow-sm">

<h5>📍 Delivery Address</h5>

<?php
$addr = $conn->query("SELECT * FROM addresses WHERE id={$order['address_id']}")->fetch_assoc();
?>

<p>
<?php echo $addr['full_name']; ?> (<?php echo $addr['phone']; ?>)<br>
<?php echo $addr['address']; ?>, <?php echo $addr['city']; ?><br>
<?php echo $addr['state']; ?> - <?php echo $addr['pincode']; ?>
</p>

</div>

<br>

<a href="orders.php" class="btn btn-secondary">⬅ Back</a>

</div>

</body>
</html>