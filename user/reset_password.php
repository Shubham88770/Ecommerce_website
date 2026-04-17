<?php
include("../config/db.php");

$email = $_GET['email'];

if(isset($_POST['reset'])){

    $otp = $_POST['otp'];
    $new_pass = md5($_POST['new_password']);

    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND otp='$otp'");

    if($res->num_rows > 0){

        $user = $res->fetch_assoc();

        if(strtotime($user['otp_expire']) < time()){
            $error = "OTP Expired!";
        }
        else{

            $conn->query("UPDATE users SET password='$new_pass', otp=NULL WHERE email='$email'");

            echo "<script>
            alert('Password Reset Successful');
            window.location='login.php';
            </script>";
            exit();
        }

    } else {
        $error = "Invalid OTP!";
    }
}
?>

<?php include("../includes/header.php"); ?>

<div class="container mt-5" style="max-width:400px;">
<div class="card p-4 shadow">

<h4 class="text-center">Reset Password</h4>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">

<input type="number" name="otp" class="form-control mb-2" placeholder="Enter OTP" required>

<input type="password" name="new_password" class="form-control mb-3" placeholder="New Password" required>

<button name="reset" class="btn btn-success w-100">
Reset Password
</button>

</form>

</div>
</div>

<?php include("../includes/footer.php"); ?>