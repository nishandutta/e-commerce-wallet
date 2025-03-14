<?php
require_once 'db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["user_name"] = $name;
        header("Location: index.php"); // Redirect to wallet page
        exit();
    } else {
        $message = "❌ Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>🔑 Login</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>

        <label>Password:</label>
        <input type="password" name="password" class="form-control" required>

        <button type="submit" class="btn btn-primary mt-2">Login</button>
    </form>

    <p class="mt-2">Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
