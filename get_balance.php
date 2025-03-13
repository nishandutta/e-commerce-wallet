<?php
include 'db.php';

$user_id = 1;
$user = $conn->query("SELECT wallet_balance FROM users WHERE id = $user_id")->fetch_assoc();

echo number_format($user['wallet_balance'], 2);
?>
