<?php
ob_start();
session_start();

// 🔥 CHECK SESSION USER
if(!isset($_SESSION['temp_user'])){
    die("❌ Session expired! Please signup again.");
}

$email = $_SESSION['temp_user']['email'];

// 🔥 MAIL LOAD
if(file_exists("../config/mail.php")){
    include("../config/mail.php");
}

// 🔥 GENERATE NEW OTP
$otp = rand(100000,999999);

// SAVE IN SESSION
$_SESSION['otp'] = $otp;
$_SESSION['otp_expire'] = time() + 300; // 5 min

// 🔥 SEND EMAIL
if(function_exists('sendOTP')){
    $sent = sendOTP($email,$otp);

    if($sent){
        header("Location: verify_otp.php?msg=resent");
        exit();
    } else {
        echo "❌ Mail sending failed!";
    }

} else {
    echo "❌ Mail function not found!";
}
?>