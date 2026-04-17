<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];

// 🔥 FETCH ORDERS + PRODUCT
$result = $conn->query("
SELECT o.*, p.name, p.image 
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN products p ON oi.product_id = p.id
WHERE o.user_id=$uid
ORDER BY o.id DESC
");
?>

<style>
.order-card{
    background:#fff;
    border-bottom:1px solid #eee;
    padding:15px;
    display:flex;
    align-items:center;
    gap:15px;
    cursor:pointer;
}

.order-card:hover{
    background:#fafafa;
}

.order-img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:5px;
}

.order-title{
    font-weight:600;
}

.order-date{
    font-size:14px;
    color:gray;
}

.review-link{
    color:#2874f0;
    font-weight:500;
    font-size:14px;
}

.arrow{
    margin-left:auto;
    font-size:20px;
    color:#555;
}
</style>

<div class="container mt-3">

<h4 class="mb-3">My Orders</h4>

<?php if($result->num_rows == 0): ?>
<p>No Orders Found</p>
<?php endif; ?>

<?php while($row = $result->fetch_assoc()): ?>

<a href="order_details.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;color:black;">

<div class="order-card">

<img src="../assets/images/<?php echo $row['image']; ?>" class="order-img">

<div>

<div class="order-date">
Delivered on <?php echo date("M d, Y", strtotime($row['created_at'])); ?>
</div>

<div class="order-title">
<?php echo $row['name']; ?>
</div>

<?php if($row['status'] == "Delivered"): ?>
<div class="review-link">Write a Review</div>
<?php endif; ?>

</div>

<div class="arrow">›</div>

</div>

</a>

<?php endwhile; ?>

</div>

<?php include("../includes/footer.php"); ?>