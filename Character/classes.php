<?php
    require 'inc/classesFunctions.php';
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
        displayHeader();

        if (isset($_GET['classId']) && is_numeric($_GET['classId'])) {
            displayClass($_GET['classId']);

            displayClasses();
        } else {
            displayClasses();
        }

        displayFooter();
        ?>
    </body>
</html>