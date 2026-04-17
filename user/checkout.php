<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/db.php");

// ❌ header yaha mat lagao (IMPORTANT)

// 🔐 LOGIN CHECK
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];


// 🔥 DELETE ADDRESS
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM addresses WHERE id=$id AND user_id=$uid");
    header("Location: checkout.php");
    exit();
}


// 🔥 SAVE ADDRESS
if(isset($_POST['save_address'])){
    $conn->query("INSERT INTO addresses(user_id,full_name,phone,address,city,state,pincode)
    VALUES($uid,'{$_POST['full_name']}','{$_POST['phone']}','{$_POST['address']}','{$_POST['city']}','{$_POST['state']}','{$_POST['pincode']}')");

    header("Location: checkout.php");
    exit();
}


// 🔥 CART TOTAL
$total = 0;

$cartQuery = $conn->query("SELECT cart.*, products.price, products.name, products.image 
FROM cart 
JOIN products ON cart.product_id=products.id 
WHERE cart.user_id=$uid");

if($cartQuery->num_rows == 0){
    echo "<div class='container mt-5'><div class='alert alert-warning'>Cart is empty</div></div>";
    exit();
}

while($row = $cartQuery->fetch_assoc()){
    $total += $row['price'] * $row['quantity'];
}


// 🔥 CONFIRM ORDER
if(isset($_POST['confirm_order'])){

    if(empty($_POST['address_id'])){
        echo "<div class='alert alert-danger'>Select address first</div>";
    } else {

        $address_id = (int)$_POST['address_id'];

        // ✅ INSERT ORDER
        $conn->query("INSERT INTO orders(user_id,total,status,address_id) 
        VALUES($uid,$total,'Pending',$address_id)");

        $order_id = $conn->insert_id;

        // ✅ INSERT ORDER ITEMS (FIXED 🔥)
        $cart = $conn->query("SELECT cart.*, products.price 
        FROM cart 
        JOIN products ON cart.product_id=products.id 
        WHERE user_id=$uid");

        while($item = $cart->fetch_assoc()){

            $conn->query("INSERT INTO order_items(order_id,product_id,quantity,price)
            VALUES($order_id,{$item['product_id']},{$item['quantity']},{$item['price']})");

        }

        // 🔥 REDIRECT TO PAYMENT
        header("Location: payment.php?order_id=$order_id&amount=$total");
        exit();
    }
}


// 🔥 FETCH ADDRESS
$addresses = $conn->query("SELECT * FROM addresses WHERE user_id=$uid ORDER BY id DESC");

// ✅ ab header include karo
include("../includes/header.php");
?>

<div class="container mt-4">
<div class="row">

<!-- LEFT -->
<div class="col-md-8">

<form method="post">

<div class="card p-3 mb-3 shadow-sm">
<h5>📍 Delivery Address</h5>

<?php while($addr = $addresses->fetch_assoc()): ?>
<div class="border p-2 mb-2">

<input type="radio" name="address_id" value="<?php echo $addr['id']; ?>" required>

<b><?php echo $addr['full_name']; ?></b>
<p><?php echo $addr['address']; ?>, <?php echo $addr['city']; ?></p>

<a href="edit_address.php?id=<?php echo $addr['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
<a href="checkout.php?delete=<?php echo $addr['id']; ?>" class="btn btn-sm btn-danger">Delete</a>

</div>
<?php endwhile; ?>

</div>

<div class="card p-3 shadow-sm">
<h5>🛒 Your Items</h5>

<?php
$cart = $conn->query("SELECT cart.*, products.name, products.price, products.image 
FROM cart 
JOIN products ON cart.product_id=products.id 
WHERE user_id=$uid");

while($item = $cart->fetch_assoc()):
?>

<div class="row mb-3 border-bottom pb-2">
<div class="col-md-3">
<img src="../assets/images/<?php echo $item['image']; ?>" class="img-fluid">
</div>

<div class="col-md-6">
<h6><?php echo $item['name']; ?></h6>
<p class="text-success">₹<?php echo $item['price']; ?></p>
<p>Qty: <?php echo $item['quantity']; ?></p>
</div>

<div class="col-md-3 text-end">
<b>₹<?php echo $item['price'] * $item['quantity']; ?></b>
</div>
</div>

<?php endwhile; ?>

</div>

</div>

<!-- RIGHT -->
<div class="col-md-4">

<div class="card p-3 shadow sticky-top">

<h5>Price Details</h5>
<hr>

<p>Price: ₹<?php echo $total; ?></p>
<p>Delivery: FREE</p>

<hr>

<h4>Total: ₹<?php echo $total; ?></h4>

<button name="confirm_order" class="btn btn-warning w-100">
Confirm Order
</button>

</div>

</div>

</form>

</div>

<hr>

<h5>➕ Add New Address</h5>

<form method="post">
<input name="full_name" class="form-control mb-2" placeholder="Full Name" required>
<input name="phone" class="form-control mb-2" placeholder="Phone" required>
<textarea name="address" class="form-control mb-2" placeholder="Address" required></textarea>
<input name="city" class="form-control mb-2" placeholder="City">
<input name="state" class="form-control mb-2" placeholder="State">
<input name="pincode" class="form-control mb-2" placeholder="Pincode">

<button type="submit" name="save_address" class="btn btn-secondary">
Save Address
</button>
</form>

</div>

<?php include("../includes/footer.php"); ?>