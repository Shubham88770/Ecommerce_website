<?php
include("../config/db.php");
include("../includes/header.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];
$id = (int)$_GET['id'];

// FETCH DATA
$data = $conn->query("SELECT * FROM addresses WHERE id=$id AND user_id=$uid")->fetch_assoc();

if(!$data){
    echo "<div class='alert alert-danger'>Address not found</div>";
    exit();
}

// UPDATE
if(isset($_POST['update'])){

    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];

    if($name != "" && $phone != "" && $address != ""){

        $conn->query("UPDATE addresses SET
        full_name='$name',
        phone='$phone',
        address='$address',
        city='$city',
        state='$state',
        pincode='$pincode'
        WHERE id=$id AND user_id=$uid");

        echo "<div class='alert alert-success'>Address Updated</div>";
        header("Refresh:1; url=checkout.php");
    }
}
?>

<div class="container">
<h3>Edit Address</h3>

<form method="post">

<input name="full_name" value="<?php echo $data['full_name']; ?>" class="form-control mb-2" required>
<input name="phone" value="<?php echo $data['phone']; ?>" class="form-control mb-2" required>
<textarea name="address" class="form-control mb-2" required><?php echo $data['address']; ?></textarea>
<input name="city" value="<?php echo $data['city']; ?>" class="form-control mb-2">
<input name="state" value="<?php echo $data['state']; ?>" class="form-control mb-2">
<input name="pincode" value="<?php echo $data['pincode']; ?>" class="form-control mb-2">

<button name="update" class="btn btn-success w-100">Update Address</button>

</form>
</div>

<?php include("../includes/footer.php"); ?>