<?php
session_start();
include("../config/db.php");

$uid = $_SESSION['user']['id'];
$pid = (int)$_GET['id'];

$conn->query("DELETE FROM wishlist WHERE user_id=$uid AND product_id=$pid");

header("Location: wishlist.php");
exit();