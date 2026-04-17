<?php
include("config/mail.php");

$result = sendOTP("YOUR_EMAIL@gmail.com", 123456);

if($result){
    echo "✅ Mail Sent Successfully";
}else{
    echo "❌ Mail Failed";
}
?>