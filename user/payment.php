<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/db.php");
include("../includes/header.php");

// 🔐 LOGIN CHECK
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];

// ⚠️ SAFE GET
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$amount   = isset($_GET['amount']) ? (int)$_GET['amount'] : 0;

// ❌ INVALID CHECK
if($order_id <= 0 || $amount <= 0){
    echo "<div class='alert alert-danger'>Invalid Request</div>";
    include("../includes/footer.php");
    exit();
}

// ✅ ORDER CHECK
$check = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id=$uid");

if(!$check || $check->num_rows == 0){
    echo "<div class='alert alert-danger'>Invalid Order</div>";
    include("../includes/footer.php");
    exit();
}

// ================= COD ORDER =================
if(isset($_POST['place_order'])){

    $payment = $_POST['payment'];

    if($payment == "cod"){

        // 🔥 STATUS CHANGE (Yaha edit kar sakte ho)
        $conn->query("UPDATE orders 
        SET status='Pending', payment_method='COD' 
        WHERE id=$order_id");

        // 🔥 CART CLEAR
        $conn->query("DELETE FROM cart WHERE user_id=$uid");

        echo "<script>
        alert('✅ Order Placed (COD)');
        window.location='orders.php';
        </script>";
        exit();
    }
}
?>

<div class="container mt-4">

<h4>⬅ Complete Payment</h4>

<form method="post" id="paymentForm">

<div class="row mt-3">

<!-- LEFT -->
<div class="col-md-8">
<div class="card p-4 shadow-sm rounded-4">

<h5 class="mb-3">💳 Payment Options</h5>
<hr>

<label class="d-block mb-3">
<input type="radio" name="payment" value="card" checked>
<strong> Online Payment (UPI / Card)</strong>
</label>

<label class="d-block mb-3">
<input type="radio" name="payment" value="cod">
<strong> Cash on Delivery</strong>
</label>

<button type="submit" name="place_order" class="btn btn-warning w-100 mt-3">
Pay ₹<?php echo $amount; ?>
</button>

</div>
</div>

<!-- RIGHT -->
<div class="col-md-4">
<div class="card p-4 shadow-sm rounded-4">

<h5>🧾 Price Details</h5>
<hr>

<p>MRP: ₹<?php echo $amount + 1500; ?></p>
<p>Discount: -₹1500</p>
<p>Platform Fee: ₹7</p>

<hr>

<h4>Total: ₹<?php echo $amount; ?></h4>

</div>
</div>

</div>

</form>

</div>

<!-- 🔥 RAZORPAY -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.getElementById("paymentForm").addEventListener("submit", function(e){

    let payment = document.querySelector('input[name="payment"]:checked').value;

    // ❌ COD → normal submit
    if(payment === "cod"){
        return;
    }

    // 🔥 ONLINE PAYMENT
    e.preventDefault();

    var options = {
        "key": "rzp_live_SbSxAx7kmNaS2O", 
        // 🔥 EDIT HERE: apni Razorpay KEY (public key only)

        "amount": "<?php echo $amount * 100; ?>", // paisa * 100
        "currency": "INR",

        "name": "SellCart",
        "description": "Order Payment",

        "handler": function (response){

            // 🔥 PAYMENT SUCCESS → redirect
            window.location.href = "success.php?order_id=<?php echo $order_id; ?>&payment_id=" + response.razorpay_payment_id;
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
});
</script>

<?php include("../includes/footer.php"); ?>