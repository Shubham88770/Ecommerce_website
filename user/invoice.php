<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/db.php");

// 🔐 LOGIN CHECK
if(!isset($_SESSION['user'])){
    die("❌ Login required");
}

$uid = $_SESSION['user']['id'];
$order_id = (int)($_GET['id'] ?? 0);

if($order_id <= 0){
    die("❌ Invalid Order ID");
}

// ✅ ORDER
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id=$uid")->fetch_assoc();

if(!$order){
    die("❌ Order not found");
}

// ✅ ADDRESS
$address = $conn->query("SELECT * FROM addresses WHERE id={$order['address_id']}")->fetch_assoc();

// ✅ ITEMS (🔥 SIZE ADD KIYA)
$items = $conn->query("
SELECT order_items.*, products.name 
FROM order_items 
JOIN products ON order_items.product_id=products.id 
WHERE order_id=$order_id
");

// 🔥 DOMPDF
require_once __DIR__ . '/../vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// 🔥 PAYMENT FIX
if($order['payment_method'] == 'COD'){
    $payment_method = "Cash on Delivery";
    $payment_status = "Pay on Delivery";
} else {
    $payment_method = "Online Payment";
    $payment_status = "Paid";
}

// 🔥 HTML
$html = '
<h2 style="text-align:center;">🧾 SellCart Invoice</h2>
<hr>

<b>Order ID:</b> #'.$order_id.'<br>
<b>Date:</b> '.$order['created_at'].'<br>

<b>Payment Method:</b> '.$payment_method.'<br>
<b>Payment Status:</b> '.$payment_status.'<br>';

if($order['payment_method'] == 'Online'){
    $html .= '<b>Transaction ID:</b> '.$order['transaction_id'].'<br>';
}

$html .= '
<br>

<h4>📍 Delivery Address</h4>
'.$address['full_name'].'<br>
'.$address['address'].'<br>
'.$address['city'].' - '.$address['pincode'].'<br><br>

<table border="1" width="100%" cellpadding="10" cellspacing="0">
<tr>
<th>Product</th>
<th>Size</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>';

$total = 0;

while($row = $items->fetch_assoc()){

    // 🔥 SIZE SAFE
    $size = !empty($row['size']) ? $row['size'] : '-';

    $line = $row['price'] * $row['quantity'];
    $total += $line;

    $html .= "
    <tr>
    <td>{$row['name']}</td>
    <td>$size</td>
    <td>{$row['quantity']}</td>
    <td>₹{$row['price']}</td>
    <td>₹$line</td>
    </tr>";
}

$html .= "
<tr>
<td colspan='4'><b>Total</b></td>
<td><b>₹$total</b></td>
</tr>
</table>

<br><br>

<p style='text-align:center; font-size:12px;'>
Thank you for shopping with <b>SwiftCart</b> ❤️
</p>
";

// 🔥 PDF GENERATE
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();

// 🔥 DOWNLOAD
$dompdf->stream("invoice_$order_id.pdf", ["Attachment" => true]);
exit();