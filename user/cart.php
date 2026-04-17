<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];

// ✅ AUTO UPDATE + AUTO DELETE
if(isset($_POST['ajax_update'])){
    $cid = (int)$_POST['cart_id'];
    $qty = (int)$_POST['qty'];

    if($qty <= 0){
        // 🔥 AUTO DELETE
        $conn->query("DELETE FROM cart WHERE id=$cid");
    } else {
        $conn->query("UPDATE cart SET quantity=$qty WHERE id=$cid");
    }
    exit();
}

// ✅ FETCH CART
$result = $conn->query("
SELECT cart.*, products.name, products.price, products.image 
FROM cart 
JOIN products ON cart.product_id=products.id 
WHERE user_id=$uid
");

$total = 0;
?>

<style>
.cart-card{
    border:1px solid #ddd;
    border-radius:10px;
    padding:15px;
    margin-bottom:15px;
    background:#fff;
}

.cart-img{
    width:100%;
    border-radius:8px;
}

.qty-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.qty-btn{
    width:32px;
    height:32px;
    border:none;
    background:#e5e7eb;
    font-weight:bold;
    border-radius:5px;
    cursor:pointer;
}

.qty-btn:hover{
    background:#d1d5db;
}

.price-box{
    background:#fff;
    padding:20px;
    border-radius:10px;
    border:1px solid #ddd;
}
</style>

<div class="container mt-4">

<div class="row">

<!-- LEFT -->
<div class="col-md-8">

<?php while($row = $result->fetch_assoc()): 
$item_total = $row['price'] * $row['quantity'];
$total += $item_total;
?>

<div class="cart-card">

<div class="row align-items-center">

<div class="col-md-3">
<img src="../assets/images/<?php echo $row['image']; ?>" class="cart-img">
</div>

<div class="col-md-6">

<h5><?php echo $row['name']; ?></h5>

<p class="mb-1">
<b>Size:</b> <?php echo $row['size']; ?>
</p>

<p class="text-success fw-bold">₹<?php echo $row['price']; ?></p>

<!-- 🔥 QTY CONTROL -->
<div class="qty-box">

<button class="qty-btn" onclick="updateQty(<?php echo $row['id']; ?>, <?php echo $row['quantity'] - 1; ?>)">-</button>

<input type="text" value="<?php echo $row['quantity']; ?>" style="width:40px;text-align:center;" readonly>

<button class="qty-btn" onclick="updateQty(<?php echo $row['id']; ?>, <?php echo $row['quantity'] + 1; ?>)">+</button>

</div>

</div>

<div class="col-md-3 text-end">
<h5>₹<?php echo $item_total; ?></h5>
</div>

</div>

</div>

<?php endwhile; ?>

<?php if($total == 0): ?>
<div class="text-center mt-5">
<h4>🛒 Cart is Empty</h4>
<p>Add products to cart</p>
</div>
<?php endif; ?>

</div>

<!-- RIGHT -->
<div class="col-md-4">

<div class="price-box">

<h5>Price Details</h5>
<hr>

<p>Price: ₹<?php echo $total; ?></p>
<p>Discount: ₹0</p>
<p>Delivery: FREE</p>

<hr>

<h4>Total: ₹<?php echo $total; ?></h4>

<?php if($total > 0): ?>
<a href="checkout.php" class="btn btn-warning w-100 mt-3">
Place Order
</a>
<?php endif; ?>

</div>

</div>

</div>

</div>

<script>
function updateQty(cart_id, qty){

    let formData = new FormData();
    formData.append("cart_id", cart_id);
    formData.append("qty", qty);
    formData.append("ajax_update", true);

    fetch("cart.php", {
        method: "POST",
        body: formData
    })
    .then(() => {
        location.reload();
    });
}
</script>

<?php include("../includes/footer.php"); ?>