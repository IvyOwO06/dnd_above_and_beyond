<?php

require '../inc/navFunctions.php';
require '../inc/creationsFunctions.php';
$userId = $_GET['userId'];
$character = getCharacters($userId);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ඞ</title>
        <link rel="stylesheet" href="../css/main.css">
    </head>
    <body>
        <?php
        displayHeader();
        dd($character);
        displayFooter();
        ?>
    </body>
</html>