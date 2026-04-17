<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();

include("../config/db.php");

// 🔥 DOMPDF LOAD (FINAL FIX)
require_once __DIR__ . '/../vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// 🔥 PHPMailer LOAD
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 🔐 LOGIN CHECK
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];

// 🔥 SAFE INPUT
$order_id = (int)($_GET['order_id'] ?? 0);
$payment_id = mysqli_real_escape_string($conn, $_GET['payment_id'] ?? '');

if($order_id <= 0 || empty($payment_id)){
    die("❌ Invalid Request");
}

// ✅ ORDER CHECK
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id=$uid")->fetch_assoc();

if(!$order){
    die("❌ Order not found");
}

// ✅ UPDATE ORDER
if($order['payment_method'] != 'Online'){

    $conn->query("UPDATE orders 
    SET status='Processing', payment_method='Online', transaction_id='$payment_id' 
    WHERE id=$order_id AND user_id=$uid");

    $conn->query("DELETE FROM cart WHERE user_id=$uid");
}

// ✅ ADDRESS
$address = $conn->query("SELECT * FROM addresses WHERE id={$order['address_id']}")->fetch_assoc();

// ✅ ITEMS
$items = $conn->query("
SELECT order_items.*, products.name 
FROM order_items 
JOIN products ON order_items.product_id=products.id 
WHERE order_id=$order_id
");

// 🔥 LOGO
$logo = "https://faithearning.in/ecommerce/assets/images/logo.png";

// 🔥 INVOICE HTML
$html = '
<h2>SwiftCart Invoice</h2>
<img src="'.$logo.'" height="60"><br><br>

<b>Order ID:</b> #'.$order_id.'<br>
<b>Date:</b> '.$order['created_at'].'<br><br>

<h4>Shipping Address</h4>
'.($address['full_name'] ?? '').'<br>
'.($address['address'] ?? '').'<br>
'.($address['city'] ?? '').' - '.($address['pincode'] ?? '').'<br><br>

<table border="1" width="100%" cellpadding="8">
<tr>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>';

$total = 0;

while($row = $items->fetch_assoc()){
    $line = $row['price'] * $row['quantity'];
    $total += $line;

    $html .= "
    <tr>
    <td>{$row['name']}</td>
    <td>{$row['quantity']}</td>
    <td>₹{$row['price']}</td>
    <td>₹$line</td>
    </tr>";
}

$html .= "
<tr>
<td colspan='3'><b>Total</b></td>
<td><b>₹$total</b></td>
</tr>
</table>
";

// 🔥 PDF GENERATE
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();

$pdf = $dompdf->output();

// 🔥 SAVE FILE (IMPORTANT FIX)
$file_name = "invoice_$order_id.pdf";
$file_path = __DIR__ . "/" . $file_name;

file_put_contents($file_path, $pdf);

// 🔥 USER EMAIL
$user = $conn->query("SELECT email FROM users WHERE id=$uid")->fetch_assoc();
$user_email = $user['email'] ?? '';

// 🔥 SEND EMAIL
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'shubhamkumar8877crpf@gmail.com';
    $mail->Password = 'yjva flag pdpm pmdv';

    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('shubhamkumar8877crpf@gmail.com', 'SwiftCart');
    $mail->addAddress($user_email);

    // 🔥 ATTACH PDF
    if(file_exists($file_path)){
        $mail->addAttachment($file_path);
    }

    $mail->isHTML(true);
    $mail->Subject = "Order Confirmed - SwiftCart";
    $mail->Body = "
    <h3>Order Placed Successfully 🎉</h3>
    <p>Your order #$order_id is confirmed.</p>
    <p>Invoice attached.</p>
    ";

    $mail->send();

} catch (Exception $e) {
    // optional debug
}
?>

<?php include("../includes/header.php"); ?>

<style>
.success-container{max-width:600px;margin:50px auto;}
.success-card{background:#fff;padding:30px;border-radius:15px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.1);}
.success-icon{font-size:60px;color:#10b981;}
.order-box{background:#f1f5f9;padding:20px;border-radius:10px;margin-top:20px;text-align:left;}
.btn-custom{border-radius:25px;padding:10px 20px;}
</style>

<div class="container success-container">
<div class="success-card">

<div class="success-icon">✅</div>

<h3>Order Placed Successfully 🎉</h3>
<p>Invoice has been sent to your email.</p>

<div class="order-box">
<p><b>Order ID:</b> #<?php echo $order_id; ?></p>
<p><b>Payment ID:</b> <?php echo $payment_id; ?></p>
<p><b>Total:</b> ₹<?php echo $order['total']; ?></p>
</div>

<div class="mt-4">
<a href="orders.php" class="btn btn-primary btn-custom">📦 Orders</a>
<a href="index.php" class="btn btn-success btn-custom">🛍 Shop</a>

<!-- 🔥 DOWNLOAD FIX -->
<a href="<?php echo $file_name; ?>" class="btn btn-dark btn-custom">
📄 Download Invoice
</a>

</div>

</div>
</div>

<?php include("../includes/footer.php"); ?>