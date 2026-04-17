<?php
ob_start();
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include("../config/db.php");

// 🔥 MAIL LOAD
if(file_exists("../config/mail.php")){
    include("../config/mail.php");
}

// 🔥 SIGNUP LOGIC (FIXED ✅)
if(isset($_POST['signup'])){

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $pass = md5($password);

    // ✅ VALIDATION
    if(empty($name) || empty($email) || empty($password)){
        $error = "❌ All fields required!";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "❌ Invalid Email Format!";
    }
    else{

        // ✅ CHECK EMAIL EXISTS
        $check = $conn->query("SELECT * FROM users WHERE email='$email'");

        if($check->num_rows > 0){
            $error = "⚠ Email already exists!";
        }
        else{

            // 🔥 STORE DATA IN SESSION (NOT DB)
            $_SESSION['temp_user'] = [
                'name' => $name,
                'email' => $email,
                'password' => $pass
            ];

            // 🔥 OTP GENERATE
            $otp = rand(100000,999999);

            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expire'] = time() + 300; // 5 min

            // 🔥 SEND OTP
            if(function_exists('sendOTP')){
                sendOTP($email,$otp);
            }

            // 🔥 REDIRECT
            header("Location: verify_otp.php");
            exit();
        }
    }
}
?>

<?php include("../includes/header.php"); ?>

<!-- 🔥 ERROR -->
<?php if(isset($error)): ?>
<div class="container mt-3">
<div class="alert alert-danger text-center"><?php echo $error; ?></div>
</div>
<?php endif; ?>

<style>
.premium-signup-container {
    min-height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.premium-signup-card {
    width: 500px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}
.premium-signup-header {
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:#fff;
    padding:30px;
    text-align:center;
}
.premium-signup-body {
    padding:30px;
}
.premium-input-field{
    border-radius:10px;
    padding:12px;
}
.premium-signup-btn{
    background:#10b981;
    color:#fff;
    border-radius:10px;
    padding:12px;
    font-weight:bold;
}
</style>

<div class="premium-signup-container">

<div class="premium-signup-card">

<div class="premium-signup-header">
<h3>🔥 Create Account</h3>
<p>Signup with OTP verification</p>
</div>

<div class="premium-signup-body">

<form method="post">

<input type="text" name="name" class="form-control mb-3 premium-input-field" placeholder="Full Name" required>

<input type="email" name="email" class="form-control mb-3 premium-input-field" placeholder="Email" required>

<input type="password" name="password" class="form-control mb-3 premium-input-field" placeholder="Password" required>

<button name="signup" class="premium-signup-btn w-100">
Create Account
</button>

</form>

<div class="text-center mt-3">
Already have account? 
<a href="login.php">Login</a>
</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>