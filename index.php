<?php
include 'db.php';

$user_id = 1;
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">E-Commerce Wallet System</h2>

        <div class="card p-3 mt-3">
            <h4>User: <?php echo $user['name']; ?></h4>
            <h5>Wallet Balance: ₹<span id="walletBalance"><?php echo number_format($user['wallet_balance'], 2); ?></span></h5>
        </div>

        <form id="purchaseForm" class="mt-4">
            <div class="mb-3">
                <label for="amount" class="form-label">Purchase Amount (₹)</label>
                <input type="number" class="form-control" id="amount" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Product Category</label>
                <select class="form-control" id="category" required>
                    <option value="A">Category A (10% Cashback)</option>
                    <option value="B">Category B (2% Cashback)</option>
                    <option value="C">Category C (7% Cashback)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Make Purchase</button>
        </form>

        <div id="message" class="mt-3"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $("#purchaseForm").submit(function (event) {
            event.preventDefault();

            let amount = $("#amount").val();
            let category = $("#category").val();

            $.post("purchase.php", { amount, category }, function (response) {
                $("#message").html(response);
                $.get("get_balance.php", function (data) {
                    $("#walletBalance").text(data);
                });
            });
        });
    </script>
</body>
</html>
