<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];

$result = $conn->query("
SELECT products.* FROM wishlist
JOIN products ON wishlist.product_id = products.id
WHERE wishlist.user_id=$uid
ORDER BY wishlist.id DESC
");
?>

<style>
.grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
}

.card{
    border:1px solid #eee;
    padding:10px;
    border-radius:10px;
    text-align:center;
}

.card img{
    width:100%;
    height:180px;
    object-fit:cover;
}
</style>

<div class="container mt-4">

<h3>❤️ My Wishlist</h3>
<hr>

<div class="grid">

<?php if($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()): ?>

<div class="card">

<img src="../assets/images/<?php echo $row['image']; ?>">

<h6><?php echo $row['name']; ?></h6>

<b>₹<?php echo $row['price']; ?></b>

<br><br>

<a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">View</a>

<a href="remove_wishlist.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Remove</a>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No items in wishlist</p>

<?php endif; ?>

</div>

</div>

<?php include("../includes/footer.php"); ?>