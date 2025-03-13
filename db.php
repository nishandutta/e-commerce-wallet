<?php
$host = "dpg-cv9gldogph6c73e8hiu0-a";
$user = "ecommerce_wallet_user";
$pass = "";
$dbname = "ecommerce_wallet";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
