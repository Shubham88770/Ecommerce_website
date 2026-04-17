<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();

include("../config/db.php");

// SAFE MAIL LOAD
if(file_exists("../config/mail.php")){
    include("../config/mail.php");
}

if(isset($_POST['send_otp'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);

    if(empty($email)){
        $error = "❌ Email required!";
    }
    else{

        $check = $conn->query("SELECT * FROM users WHERE email='$email'");

        if($check && $check->num_rows > 0){

            $otp = rand(100000,999999);
            $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            $conn->query("UPDATE users SET otp='$otp', otp_expire='$expire' WHERE email='$email'");

            // SAFE OTP SEND
            if(function_exists('sendOTP')){
                sendOTP($email,$otp);
            }

            header("Location: reset_password.php?email=$email");
            exit();

        } else {
            $error = "❌ Email not found!";
        }
    }
}
?>

<?php include("../includes/header.php"); ?>

<div class="container mt-5" style="max-width:400px;">
<div class="card p-4 shadow">

<h4>Forgot Password</h4>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">

<input type="email" name="email" class="form-control mb-3" placeholder="Enter Email" required>

<button name="send_otp" class="btn btn-primary w-100">
Send OTP
</button>

</form>

</div>
</div>

<?php include("../includes/footer.php"); ?>