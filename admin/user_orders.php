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

// FETCH ALL ORDERS + USER
$orders = $conn->query("
SELECT orders.*, users.name AS user_name 
FROM orders 
JOIN users ON orders.user_id = users.id 
ORDER BY orders.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>All Users Orders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f5f7fa; }
.card { border-radius:15px; }
</style>

</head>

<body>

<div class="container mt-5">

<h3 class="mb-4">📦 All Users Orders</h3>

<?php while($order = $orders->fetch_assoc()): ?>

<div class="card p-3 mb-4 shadow-sm">

<!-- 🔥 ORDER HEADER -->
<div class="d-flex justify-content-between">
    <div>
        <b>Order ID:</b> #<?php echo $order['id']; ?><br>
        <b>User:</b> <?php echo $order['user_name']; ?><br>
        <b>Total:</b> ₹<?php echo $order['total']; ?>
    </div>

    <div>
        <span class="badge bg-info"><?php echo $order['status']; ?></span><br>
        <small><?php echo $order['created_at']; ?></small>
    </div>
</div>

<hr>

<!-- 🔥 ORDER ITEMS -->
<h6>Items:</h6>

<?php
$items = $conn->query("
SELECT order_items.*, products.name, products.image 
FROM order_items 
JOIN products ON order_items.product_id = products.id 
WHERE order_id = {$order['id']}
");

while($item = $items->fetch_assoc()):
?>

<div class="row mb-2 align-items-center">

<div class="col-md-2">
<img src="../assets/images/<?php echo $item['image']; ?>" width="60">
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

<hr>

<!-- 🔥 ADDRESS -->
<?php
$addr = $conn->query("SELECT * FROM addresses WHERE id={$order['address_id']}")->fetch_assoc();
?>

<h6>📍 Delivery Address:</h6>
<p>
<?php echo $addr['full_name']; ?> (<?php echo $addr['phone']; ?>)<br>
<?php echo $addr['address']; ?>, <?php echo $addr['city']; ?><br>
<?php echo $addr['state']; ?> - <?php echo $addr['pincode']; ?>
</p>

</div>

<?php endwhile; ?>

</div>

</body>
</html>