<?php
session_start();
require_once 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password
    $wallet_balance = 100.00; // Default balance

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $error = "❌ Email already registered!";
    } else {
        // Insert new user with default wallet balance
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, wallet_balance) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $name, $email, $password, $wallet_balance);
        
        if ($stmt->execute()) {
            $success = "🎉 Registration successful! You have ₹100 in your wallet. <a href='login.php'>Login here</a>";
        } else {
            $error = "❌ Error registering user. Please try again.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>📝 Register</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="form-label">Name:</label>
        <input type="text" name="name" class="form-control" required>

        <label class="form-label mt-2">Email:</label>
        <input type="email" name="email" class="form-control" required>

        <label class="form-label mt-2">Password:</label>
        <input type="password" name="password" class="form-control" required>

        <button type="submit" class="btn btn-primary mt-3">Register</button>
    </form>

    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
