<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user = null;
$cashback_message = "";

// Fetch user details
$stmt = $conn->prepare("SELECT name, wallet_balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
$stmt->close();

// ✅ Apply Cashback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_cashback'])) {
    $purchase_amount = floatval($_POST['purchase_amount']);
    $category = $_POST['category'];

    // Cashback rates
    $cashback_rates = [
        'A' => 10, // 10% for Category A
        'B' => 2,  // 2% for Category B
        'C' => 7   // 7% for Category C
    ];

    if (isset($cashback_rates[$category])) {
        $cashback_amount = ($purchase_amount * $cashback_rates[$category]) / 100;

        // Update wallet balance securely
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
        $stmt->bind_param("di", $cashback_amount, $user_id);
        if ($stmt->execute()) {
            // Fetch updated balance
            $stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user['wallet_balance'] = $result->fetch_assoc()['wallet_balance'];
            }
            $stmt->close();

            $cashback_message = "🎉 ₹" . number_format($cashback_amount, 2) . " cashback added to your wallet!";
        } else {
            $cashback_message = "❌ Error updating wallet.";
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
    <div class="d-flex justify-content-between align-items-center">
        <h2>🛒 E-commerce Wallet</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Display Wallet Balance -->
    <?php if ($user): ?>
        <div class="alert alert-success mt-3">
            <h4>👤 User: <?= htmlspecialchars($user['name']) ?></h4>
            <p>💰 Wallet Balance: ₹<?= number_format((float)$user['wallet_balance'], 2) ?></p>
        </div>
    <?php endif; ?>

    <!-- Cashback Application Form -->
    <form method="POST" class="mb-3">
        <label for="purchase_amount" class="form-label">Enter Purchase Amount:</label>
        <input type="number" name="purchase_amount" id="purchase_amount" class="form-control" required>

        <label for="category" class="form-label mt-2">Select Product Category:</label>
        <select name="category" id="category" class="form-control" required>
            <option value="A">Category A (10% Cashback)</option>
            <option value="B">Category B (2% Cashback)</option>
            <option value="C">Category C (7% Cashback)</option>
        </select>

        <button type="submit" name="apply_cashback" class="btn btn-success mt-3">Apply Cashback</button>
    </form>

    <!-- Display Cashback Message -->
    <?php if ($cashback_message): ?>
        <div class="alert alert-info"><?= $cashback_message ?></div>
    <?php endif; ?>

</body>
</html>
