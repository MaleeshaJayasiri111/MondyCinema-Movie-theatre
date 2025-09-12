<?php
include_once('config.php');
include_once('BO/User.php');

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        if (User::Register($name, $email, $password)) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Email may already be in use.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/register.css">
</head>
<body>
    <header>
        <div class="logo">MondyCinema</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>
    
    <main class="container">
        <div class="form-container">
            <h2>Register</h2>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <form method="post">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">Register</button>
            </form>
            
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 MondyCinema. All rights reserved.</p>
    </footer>
</body>
</html>