<?php
// Include database connection
require_once 'db.php';

$user = null;
$error = "";
$cashback_message = "";

// Check if form is submitted for user balance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_balance'])) {
    $user_id = intval($_POST['user_id']); // Convert input to integer

    // Query database
    $result = $conn->query("SELECT name, wallet_balance FROM users WHERE id = $user_id");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $error = "❌ No user found with ID $user_id.";
    }
}

// Cashback Calculation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_cashback'])) {
    $user_id = intval($_POST['user_id']);
    $purchase_amount = floatval($_POST['purchase_amount']);
    $category = $_POST['category'];

    // Define cashback percentages
    $cashback_rates = [
        'A' => 10, // 10% for Category A
        'B' => 2,  // 2% for Category B
        'C' => 7   // 7% for Category C
    ];

    if (isset($cashback_rates[$category])) {
        $cashback_amount = ($purchase_amount * $cashback_rates[$category]) / 100;

        // Update user's wallet balance
        $update_query = $conn->query("UPDATE users SET wallet_balance = wallet_balance + $cashback_amount WHERE id = $user_id");

        if (!$update_query) {
            $cashback_message = "❌ Error updating wallet: " . $conn->error;
        }
        
        if ($update_query) {
            $cashback_message = "🎉 Cashback of ₹" . number_format($cashback_amount, 2) . " added to your wallet!";
        } else {
            $cashback_message = "❌ Failed to apply cashback. Please try again.";
        }
    } else {
        $cashback_message = "❌ Invalid category selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Wallet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-3">🛒 E-commerce Wallet</h2>

    <!-- User ID Input Form -->
    <form method="POST" class="mb-3">
        <label for="user_id" class="form-label">Enter User ID:</label>
        <input type="number" name="user_id" id="user_id" class="form-control" required>
        <button type="submit" name="check_balance" class="btn btn-primary mt-2">Check Balance</button>
    </form>

    <!-- Display Wallet Balance -->
    <?php if ($user): ?>
        <div class="alert alert-success">
            <h4>👤 User: <?= htmlspecialchars($user['name']) ?></h4>
            <p>💰 Wallet Balance: ₹<?= number_format((float)$user['wallet_balance'], 2) ?></p>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Cashback Application Form -->
    <form method="POST" class="mb-3">
    <input type="hidden" name="user_id" value="<?= isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : '' ?>">
        
        <label for="purchase_amount" class="form-label">Enter Purchase Amount:</label>
        <input type="number" name="purchase_amount" id="purchase_amount" class="form-control" required>

        <label for="category" class="form-label mt-2">Select Product Category:</label>
        <select name="category" id="category" class="form-control" required>
            <option value="A">Category A (10% Cashback)</option>
            <option value="B">Category B (2% Cashback)</option>
            <option value="C">Category C (7% Cashback)</option>
        </select>

        <button type="submit" name="apply_cashback" class="btn btn-success mt-2">Apply Cashback</button>
    </form>

    <!-- Display Cashback Result -->
    <?php if ($cashback_message): ?>
        <div class="alert alert-info"><?= $cashback_message ?></div>
    <?php endif; ?>

</body>
</html>
