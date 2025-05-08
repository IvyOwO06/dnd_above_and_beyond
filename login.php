<?php

require 'inc/loginFunctions.php';
session_start();

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
    <title>Login</title>
</head>

<body class="login">
    <main>
        <form method="POST" action="login.php">
            <h2>LOGIN</h2>
            <?php if (isset($_POST['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p> <?php } ?>
            <label>User Name</label>
            <input type="text" name="uname" placeholder="User Name"><br>
            <label>Password</label>
            <input type="password" name="password" placeholder="Password"><br>
            <button class="btn" type="submit">Login</button>
            <a href="register.php">Sign Up</a>
        </form>
    </main>
</body>

</html>