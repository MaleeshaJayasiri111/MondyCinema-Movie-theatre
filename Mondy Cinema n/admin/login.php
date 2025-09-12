<?php
session_start();
if (isset($_SESSION['loggedin'])) {
    header("Location: dashboard.php");
    exit;
}
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === 'Mondy Cinema' && $password === 'Admin@2025') {
        $_SESSION['loggedin'] = true;
        $_SESSION['success_message'] = "You're successfully logged in to admin dashboard";
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Incorrect username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/style/admin-login.css">
</head>
<body class="login-body">
    <h1 class="app-name">Mondy Cinema</h1>
    <div class="login-container">
        <h2 class="login-title">Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="button-group">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
