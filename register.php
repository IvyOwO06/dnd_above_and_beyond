<?php

require 'inc/loginFunctions.php';
require 'inc/navFunctions.php';

signup();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/signup.css">
    <title>Sign-Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>
    <?php
    displayHeader();
    ?>
    <main>
  <div class="login-form">
    <h2>SIGN UP</h2>

    <?php if (isset($_GET['error'])): ?>
      <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <label for="user">Username</label>
      <input type="text" id="user" name="user" placeholder="Enter Username" required>

      <label for="email">Email</label>
      <input type="text" id="email" name="email" placeholder="Enter Email" required>

      <label for="pass">Password</label>
      <input type="password" id="pass" name="pass" placeholder="Create Password" required>

      <label for="cpass">Confirm Password</label>
      <input type="password" id="cpass" name="cpass" placeholder="Retype Password" required>

      <button class="small-button" type="submit">Sign Up</button>
      <a href="login.php" class="small-button">Go to Login</a>
    </form>
  </div>
</main>

    <?php
    displayFooter();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>