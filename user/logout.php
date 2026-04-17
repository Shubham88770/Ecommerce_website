<?php
session_start();

// remove session
$_SESSION = [];

// destroy session
session_destroy();

// prevent back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// 🔥 FIXED REDIRECT (IMPORTANT)
header("Location: login.php");
exit();
?>