<?php
    require '../inc/racesFunctions.php';
    require '../inc/navFunctions.php';
    require '../inc/functions.php'
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="../css/main.css">
    </head>
    <body>
        <?php
        displayHeader();

        if (isset($_GET['raceId']) && is_numeric($_GET['raceId'])) {
            displayRace($_GET['raceId']);
            displayRaces();
        } else {
            displayRaces();
        }

        displayFooter();
        ?>
    </body>
</html>