<?php
include("../config/db.php");
include("../includes/header.php");

// ✅ SEARCH
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// ✅ PRICE FILTER
$min = isset($_GET['min']) ? (int)$_GET['min'] : 0;
$max = isset($_GET['max']) ? (int)$_GET['max'] : 100000;

if(empty($q)){
    echo "<h4 class='text-danger text-center mt-4'>❌ Enter something to search</h4>";
    exit();
}

// 🔥 KEYWORD SEARCH
$keywords = explode(" ", $q);

$where = [];
foreach($keywords as $word){
    $word = mysqli_real_escape_string($conn, $word);
    $where[] = "(name LIKE '%$word%' OR description LIKE '%$word%')";
}

$where_sql = implode(" AND ", $where);

// 🔥 FINAL QUERY WITH PRICE FILTER
$result = $conn->query("
SELECT * FROM products 
WHERE $where_sql 
AND price BETWEEN $min AND $max
ORDER BY id DESC
");
?>

<style>
.search-layout{
    display:flex;
    gap:20px;
}

/* LEFT FILTER */
.filter-box{
    width:250px;
    border:1px solid #ddd;
    padding:15px;
    border-radius:10px;
    height:fit-content;
}

/* RIGHT PRODUCTS */
.product-grid{
    flex:1;
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
}

/* PRODUCT CARD */
.product-card{
    border:1px solid #eee;
    border-radius:10px;
    padding:10px;
    transition:0.2s;
    background:#fff;
}

.product-card:hover{
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.product-img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:8px;
}

.product-title{
    font-size:14px;
    margin-top:10px;
    height:40px;
    overflow:hidden;
}

.product-price{
    color:green;
    font-weight:bold;
    font-size:16px;
}

/* BUTTON */
.filter-btn{
    width:100%;
    background:#2874f0;
    color:#fff;
    border:none;
    padding:8px;
    border-radius:5px;
}
</style>

<div class="container mt-4">

<h4>🔍 Search Results for "<b><?php echo htmlspecialchars($q); ?></b>"</h4>

<div class="search-layout mt-3">

<!-- 🔥 LEFT FILTER -->
<div class="filter-box">

<h5>💰 Price Filter</h5>

<form method="get">

<input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">

<label>Min Price</label>
<input type="number" name="min" class="form-control mb-2" value="<?php echo $min; ?>">

<label>Max Price</label>
<input type="number" name="max" class="form-control mb-2" value="<?php echo $max; ?>">

<button class="filter-btn">Apply Filter</button>

</form>

</div>

<!-- 🔥 PRODUCTS -->
<div class="product-grid">

<?php if($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()): ?>

<a href="product.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;color:black;">

<div class="product-card">

<img src="../assets/images/<?php echo $row['image']; ?>" class="product-img">

<div class="product-title">
<?php echo $row['name']; ?>
</div>

<div class="product-price">
₹<?php echo $row['price']; ?>
</div>

</div>

</a>

<?php endwhile; ?>

<?php else: ?>

<div>
<h5>❌ No products found</h5>
<p>Try different keyword or price range</p>
</div>

<?php endif; ?>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>