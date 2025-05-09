<?php
require 'inc/builderFunctions.php';
require 'inc/classesFunctions.php';
require 'inc/racesFunctions.php';
require 'inc/navFunctions.php';
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
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';

    displayHeader();

    handleCharacterCreation();
    homeTabBuilder();

    displayFooter();
    ?>
</body>

</html>