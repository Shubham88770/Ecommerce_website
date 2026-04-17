<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];
$pid = (int)$_GET['id'];

// ✅ CHECK ALREADY EXISTS
$check = $conn->query("SELECT * FROM wishlist WHERE user_id=$uid AND product_id=$pid");

if($check->num_rows == 0){
    $conn->query("INSERT INTO wishlist(user_id,product_id) VALUES($uid,$pid)");
}

header("Location: wishlist.php");
exit();