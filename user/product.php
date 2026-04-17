<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if(!$product){
    echo "<div class='alert alert-danger'>Product not found</div>";
    exit();
}

// ✅ SIZE
$sizes = [
    "S"   => $product['size_s'],
    "M"   => $product['size_m'],
    "L"   => $product['size_l'],
    "XL"  => $product['size_xl'],
    "XXL" => $product['size_xxl']
];

$message = '';
$type = '';

// ✅ ADD TO CART
if(isset($_POST['add'])){
    if(!isset($_SESSION['user'])){
        $message = "Login First";
        $type = "danger";
    } else {

        $uid = $_SESSION['user']['id'];
        $size = $_POST['size'];

        if(empty($size)){
            $message = "Select Size";
            $type = "danger";
        } else {

            $conn->query("INSERT INTO cart(user_id,product_id,quantity,size)
            VALUES($uid,$id,1,'$size')");

            $message = "Added to Cart";
            $type = "success";
        }
    }
}
?>

<style>
.size-box{
    display:inline-block;
    width:55px;
    height:55px;
    line-height:55px;
    text-align:center;
    border:1px solid #ccc;
    margin:5px;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}

.size-box.active{
    background:black;
    color:#fff;
}

.size-box.disabled{
    background:#eee;
    color:#999;
    text-decoration:line-through;
    cursor:not-allowed;
}

.btn-cart{
    background:#ff9f00;
    color:#fff;
    border:none;
    padding:12px;
    width:48%;
    border-radius:8px;
}

.btn-buy{
    background:#fb641b;
    color:#fff;
    border:none;
    padding:12px;
    width:48%;
    border-radius:8px;
}
</style>

<div class="container mt-4">

<?php if($message): ?>
<div class="alert alert-<?php echo $type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">

<div class="col-md-6">
<img src="../assets/images/<?php echo $product['image']; ?>" class="img-fluid">
</div>

<div class="col-md-6">

<h3><?php echo $product['name']; ?></h3>
<h4 class="text-success">₹<?php echo $product['price']; ?></h4>

<form method="post" id="cartForm">

<h5>Select Size</h5>

<?php foreach($sizes as $size => $available): ?>

<?php if($available): ?>
<div class="size-box" onclick="selectSize(this,'<?php echo $size; ?>')">
<?php echo $size; ?>
</div>
<?php else: ?>
<div class="size-box disabled"><?php echo $size; ?></div>
<?php endif; ?>

<?php endforeach; ?>

<input type="hidden" name="size" id="sizeInput">

<br><br>

<div class="d-flex justify-content-between">

<button name="add" class="btn-cart">Add to Cart</button>

<!-- 🔥 BUY NOW FIX -->
<button type="button" class="btn-buy" onclick="buyNow()">
Buy Now
</button>

</div>

</form>

</div>

</div>

</div>

<script>
function selectSize(el,val){

    if(el.classList.contains("disabled")) return;

    document.querySelectorAll('.size-box').forEach(e=>e.classList.remove('active'));

    el.classList.add('active');

    document.getElementById('sizeInput').value = val;
}

// 🔥 FINAL BUY NOW FIX
function buyNow(){

    let size = document.getElementById("sizeInput").value;

    if(size == ""){
        alert("Please select size");
        return;
    }

    // 🔥 REDIRECT WITH DATA
    window.location.href = "checkout.php?buy_now=1&id=<?php echo $id; ?>&size=" + size;
}
</script>

<?php include("../includes/footer.php"); ?>