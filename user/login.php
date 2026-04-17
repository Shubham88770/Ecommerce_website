<?php
ob_start();
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include("../config/db.php");

// 🔥 LOGIN LOGIC
if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($res && $res->num_rows > 0){

        $user = $res->fetch_assoc();

        // 🔐 EMAIL VERIFY CHECK
        if(isset($user['is_verified']) && $user['is_verified'] == 0){
            $error = "⚠ Please verify your email first!";
        }

        // 🔐 PASSWORD CHECK
        else if($user['password'] == md5($password) || $user['password'] == $password){

            $_SESSION['user'] = $user;

            header("Location: index.php");
            exit();

        } else {
            $error = "❌ Wrong Password!";
        }

    } else {
        $error = "❌ User Not Found!";
    }
}
?>

<?php include("../includes/header.php"); ?>

<style>
.premium-login-container {
    min-height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.premium-login-card {
    width: 420px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}

.login-header {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: #fff;
    padding: 30px;
    text-align: center;
}

.login-body {
    padding: 30px;
}

.form-control {
    border-radius: 10px;
    height: 45px;
}

.btn-login {
    background: #fb641b;
    color: #fff;
    border-radius: 25px;
    height: 45px;
    font-weight: bold;
}

.btn-login:hover {
    background: #e85d04;
}

.error-box {
    background: #ffe0e0;
    color: red;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
}

.link-box {
    text-align: center;
    margin-top: 15px;
}
</style>

<div class="premium-login-container">

<div class="premium-login-card">

<div class="login-header">
<h3>🔐 Welcome Back</h3>
<p>Login to your account</p>
</div>

<div class="login-body">

<?php if(isset($error)): ?>
<div class="error-box"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">

<input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

<input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

<button type="submit" name="login" class="btn btn-login w-100">
Login
</button>

</form>

<!-- 🔥 FORGOT PASSWORD -->
<div class="link-box">
<a href="forgot_password.php" style="color:#fb641b; font-weight:bold;">
Forgot Password?
</a>
</div>

<!-- 🔥 SIGNUP -->
<div class="link-box">
New user? 
<a href="register.php" style="font-weight:bold; color:#2563eb;">
Create account →
</a>
</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>