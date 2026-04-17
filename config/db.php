<?php
$conn = new mysqli("localhost", "rushpayl_pro", "#@Kumar8877", "rushpayl_pro");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

echo "";
?>