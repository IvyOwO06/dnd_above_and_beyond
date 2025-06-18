<?php
require 'inc/loginFunctions.php';
require 'inc/navFunctions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>
<body>
    <?php displayHeader(); ?>
    <main>
        <form method="POST">
            <h2>LOGIN</h2>
            <?php if (isset($_GET['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <label>User Name</label>
            <input type="text" name="uname" placeholder="User Name"><br>
            <label>Password</label>
            <input type="password" name="password" placeholder="Password"><br>
            <button class="small-button" type="submit">Login</button>
            <a href="register.php" class="small-button">Sign Up</a>
        </form>
    </main>
    <?php displayFooter(); ?>
</body>
</html>