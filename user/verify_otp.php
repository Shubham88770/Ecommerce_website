<?php
ob_start();
session_start();
include("../config/db.php");

// ❌ SESSION CHECK
if(!isset($_SESSION['temp_user'])){
    die("<div style='text-align:center;margin-top:50px'>
    ❌ Session expired! <br><a href='register.php'>Go Back</a></div>");
}

// 🔥 VERIFY OTP
if(isset($_POST['verify'])){

    $otp = trim($_POST['otp']);

    if(empty($otp)){
        $error = "❌ Enter OTP!";
    }

    // ✅ OTP MATCH
    elseif($otp == $_SESSION['otp']){

        // ✅ EXPIRE CHECK
        if(time() > $_SESSION['otp_expire']){
            $error = "❌ OTP Expired!";
        }
        else{

            $name = mysqli_real_escape_string($conn,$_SESSION['temp_user']['name']);
            $email = mysqli_real_escape_string($conn,$_SESSION['temp_user']['email']);
            $password = $_SESSION['temp_user']['password'];

            // ✅ DOUBLE CHECK EMAIL (IMPORTANT)
            $check = $conn->query("SELECT * FROM users WHERE email='$email'");

            if($check->num_rows == 0){

                // ✅ FINAL INSERT
                $conn->query("INSERT INTO users(name,email,password,is_verified)
                VALUES('$name','$email','$password',1)");
            }

            // 🔥 SESSION CLEAR
            unset($_SESSION['temp_user']);
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expire']);

            echo "<script>
            alert('✅ Account Created Successfully');
            window.location='login.php';
            </script>";
            exit();
        }

    } else {
        $error = "❌ Invalid OTP!";
    }
}
?>

<?php include("../includes/header.php"); ?>

<div class="container mt-5" style="max-width:400px;">
<div class="card p-4 shadow text-center">

<h4 class="mb-3">🔐 Verify OTP</h4>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">

<input type="number" name="otp" class="form-control mb-3 text-center" placeholder="Enter OTP" required>

<button name="verify" class="btn btn-success w-100 mb-2">
Verify OTP
</button>

</form>

<!-- 🔥 RESEND BUTTON -->
<button id="resendBtn" class="btn btn-warning w-100" disabled>
Resend OTP (<span id="timer">30</span>s)
</button>

</div>
</div>

<!-- 🔥 TIMER -->
<script>
let timeLeft = 30;
const timer = document.getElementById("timer");
const btn = document.getElementById("resendBtn");

let countdown = setInterval(() => {
    timeLeft--;
    timer.innerText = timeLeft;

    if(timeLeft <= 0){
        clearInterval(countdown);
        btn.disabled = false;
        btn.innerText = "Resend OTP";
    }
}, 1000);

// 🔥 RESEND CLICK (IMPORTANT CHANGE)
btn.addEventListener("click", () => {
    window.location.href = "resend_otp.php";
});
</script>

<?php include("../includes/footer.php"); ?>