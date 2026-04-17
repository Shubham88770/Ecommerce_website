<?php
session_start();

if(isset($_SESSION['admin'])){
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - MyShop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
        }

        .center-box {
            height: 100vh;
        }

        .card {
            border-radius: 20px;
        }
    </style>
</head>

<body>

<div class="d-flex justify-content-center align-items-center center-box">
    <div class="card shadow p-4 text-center" style="width: 350px;">
        
        <h2 class="mb-3">⚙️ Admin Panel</h2>
        <p class="text-muted">Manage your store easily</p>

        <a href="login.php" class="btn btn-dark w-100 mt-3">Go to Login</a>

    </div>
</div>

</body>
</html>