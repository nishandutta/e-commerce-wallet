<?php
include 'db.php';

$user_id = 1;
$amount = $_POST['amount'];
$category = $_POST['category'];

// Cashback usage rates
$use_cashback_rates = [
    "A" => 5,   // 5%
    "B" => 1,   // 1%
    "C" => 3    // 3%
];

$wallet_balance = $conn->query("SELECT wallet_balance FROM users WHERE id = $user_id")->fetch_assoc()['wallet_balance'];

$max_cashback_usage = ($amount * $use_cashback_rates[$category]) / 100;
$cashback_used = min($wallet_balance, $max_cashback_usage);
$final_amount = $amount - $cashback_used;

// Deduct cashback from wallet
$conn->query("UPDATE users SET wallet_balance = wallet_balance - $cashback_used WHERE id = $user_id");

// Log transaction
$conn->query("INSERT INTO transactions (user_id, type, amount, description) VALUES ($user_id, 'purchase', -$cashback_used, 'Used cashback for purchase')");

echo "<div class='alert alert-info'>Cashback of ₹$cashback_used applied. Final amount: ₹$final_amount.</div>";
?>
