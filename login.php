<?php
session_start();
include_once('config.php');
include_once('BO/User.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::Login($email, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        if (isset($_SESSION['redirect_to'])) {
            $redirect = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
            header("Location: $redirect");
        } else {
            header("Location: bookseats.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/user-login.css">
</head>
<body>
    <header>
        <div class="logo">MondyCinema</div>
        <nav>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php">Home</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <main class="container">
        <div class="form-container">
            <h2>Login</h2>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <form method="post">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Login</button>
            </form>
            
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 MondyCinema. All rights reserved.</p>
    </footer>
</body>
</html>