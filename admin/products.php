<?php
include("../config/db.php");
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$success = "";

// ✅ ADD PRODUCT
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];
    $img = $_FILES['img']['name'];

    $size_s  = isset($_POST['size_s']) ? 1 : 0;
    $size_m  = isset($_POST['size_m']) ? 1 : 0;
    $size_l  = isset($_POST['size_l']) ? 1 : 0;
    $size_xl = isset($_POST['size_xl']) ? 1 : 0;
    $size_xxl= isset($_POST['size_xxl']) ? 1 : 0;

    move_uploaded_file($_FILES['img']['tmp_name'], "../assets/images/".$img);

    $conn->query("INSERT INTO products(name,price,description,image,size_s,size_m,size_l,size_xl,size_xxl)
    VALUES('$name','$price','$desc','$img','$size_s','$size_m','$size_l','$size_xl','$size_xxl')");

    $success = "Product Added";
}

// ✅ DELETE
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit();
}

// ✅ EDIT FETCH
$edit_data = null;
if(isset($_GET['edit'])){
    $eid = (int)$_GET['edit'];
    $edit_data = $conn->query("SELECT * FROM products WHERE id=$eid")->fetch_assoc();
}

// ✅ UPDATE
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];

    $size_s  = isset($_POST['size_s']) ? 1 : 0;
    $size_m  = isset($_POST['size_m']) ? 1 : 0;
    $size_l  = isset($_POST['size_l']) ? 1 : 0;
    $size_xl = isset($_POST['size_xl']) ? 1 : 0;
    $size_xxl= isset($_POST['size_xxl']) ? 1 : 0;

    if(!empty($_FILES['img']['name'])){
        $img = $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], "../assets/images/".$img);

        $conn->query("UPDATE products SET 
        name='$name', price='$price', description='$desc', image='$img',
        size_s='$size_s', size_m='$size_m', size_l='$size_l', size_xl='$size_xl', size_xxl='$size_xxl'
        WHERE id=$id");

    } else {
        $conn->query("UPDATE products SET 
        name='$name', price='$price', description='$desc',
        size_s='$size_s', size_m='$size_m', size_l='$size_l', size_xl='$size_xl', size_xxl='$size_xxl'
        WHERE id=$id");
    }

    header("Location: products.php");
    exit();
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Products</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>🔥 Add / Edit Product</h3>

<form method="post" enctype="multipart/form-data" class="card p-3 mb-4">

<input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? ''; ?>">

<input name="name" class="form-control mb-2" placeholder="Name"
value="<?php echo $edit_data['name'] ?? ''; ?>" required>

<input name="price" class="form-control mb-2" placeholder="Price"
value="<?php echo $edit_data['price'] ?? ''; ?>" required>

<textarea name="desc" class="form-control mb-2"><?php echo $edit_data['description'] ?? ''; ?></textarea>

<!-- SIZE -->
<label><b>Sizes</b></label><br>

<label><input type="checkbox" name="size_s" <?php if(($edit_data['size_s'] ?? 0)) echo "checked"; ?>> S</label>
<label><input type="checkbox" name="size_m" <?php if(($edit_data['size_m'] ?? 0)) echo "checked"; ?>> M</label>
<label><input type="checkbox" name="size_l" <?php if(($edit_data['size_l'] ?? 0)) echo "checked"; ?>> L</label>
<label><input type="checkbox" name="size_xl" <?php if(($edit_data['size_xl'] ?? 0)) echo "checked"; ?>> XL</label>
<label><input type="checkbox" name="size_xxl" <?php if(($edit_data['size_xxl'] ?? 0)) echo "checked"; ?>> XXL</label>

<br><br>

<input type="file" name="img" class="form-control mb-2">

<?php if($edit_data): ?>
<button name="update" class="btn btn-primary">Update Product</button>
<?php else: ?>
<button name="add" class="btn btn-success">Add Product</button>
<?php endif; ?>

</form>

<?php if($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<hr>

<h3>📦 Products</h3>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Sizes</th>
<th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>
<img src="../assets/images/<?php echo $row['image']; ?>" width="60">
</td>

<td><?php echo $row['name']; ?></td>

<td>₹<?php echo $row['price']; ?></td>

<td>
<?php
$s = [];
if($row['size_s']) $s[]='S';
if($row['size_m']) $s[]='M';
if($row['size_l']) $s[]='L';
if($row['size_xl']) $s[]='XL';
if($row['size_xxl']) $s[]='XXL';

echo implode(", ", $s);
?>
</td>

<td>
<a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
</td>

</tr>

<?php endwhile; ?>

</table>

</div>

</body>
</html>