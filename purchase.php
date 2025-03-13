<?php
include 'db.php';

$user_id = 1;
$amount = $_POST['amount'];
$category = $_POST['category'];

// Cashback rules
$cashback_rates = [
    "A" => 10,  // 10%
    "B" => 2,   // 2%
    "C" => 7    // 7%
];

$cashback_percent = isset($cashback_rates[$category]) ? $cashback_rates[$category] : 0;
$cashback = ($amount * $cashback_percent) / 100;

// Insert order
$conn->query("INSERT INTO orders (user_id, total_amount, category, cashback_earned) VALUES ($user_id, $amount, '$category', $cashback)");

// Update wallet balance
$conn->query("UPDATE users SET wallet_balance = wallet_balance + $cashback WHERE id = $user_id");

// Log transaction
$conn->query("INSERT INTO transactions (user_id, type, amount, description) VALUES ($user_id, 'cashback', $cashback, 'Cashback earned from purchase')");

echo "<div class='alert alert-success'>Purchase successful! ₹$cashback cashback added to your wallet.</div>";
?>
