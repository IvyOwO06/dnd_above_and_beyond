<?php
require 'inc/navFunctions.php';

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
} else {
    header("location: index.php");
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <?php
    displayHeader();

    dd(getUser($userId));

    displayFooter();
    ?>
</body>

</html>