<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/db.php");

// 🔐 ADMIN CHECK (optional but recommended)
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// GET PRODUCT ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id == 0){
    die("Invalid Product ID");
}

// FETCH PRODUCT
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if(!$product){
    die("Product not found");
}

// 🔥 UPDATE PRODUCT
if(isset($_POST['update'])){

    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    // IMAGE UPLOAD
    if($_FILES['image']['name']){
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/".$image);
    } else {
        $image = $product['image']; // old image
    }

    $conn->query("UPDATE products SET 
        name='$name',
        price='$price',
        description='$desc',
        image='$image'
        WHERE id=$id
    ");

    echo "<script>alert('Product Updated Successfully'); window.location='products.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f5f7fa; }
.card { border-radius:15px; }
</style>

</head>

<body>

<div class="container mt-5">

<div class="card p-4 shadow">

<h3 class="mb-3">✏️ Edit Product</h3>

<form method="post" enctype="multipart/form-data">

<div class="mb-3">
<label>Product Name</label>
<input type="text" name="name" class="form-control" 
value="<?php echo $product['name']; ?>" required>
</div>

<div class="mb-3">
<label>Price</label>
<input type="number" name="price" class="form-control" 
value="<?php echo $product['price']; ?>" required>
</div>

<div class="mb-3">
<label>Description</label>
<textarea name="description" class="form-control" required><?php echo $product['description']; ?></textarea>
</div>

<div class="mb-3">
<label>Current Image</label><br>
<img src="../assets/images/<?php echo $product['image']; ?>" width="100">
</div>

<div class="mb-3">
<label>Change Image</label>
<input type="file" name="image" class="form-control">
</div>

<button name="update" class="btn btn-success">Update Product</button>
<a href="products.php" class="btn btn-secondary">Back</a>

</form>

</div>

</div>

</body>
</html>