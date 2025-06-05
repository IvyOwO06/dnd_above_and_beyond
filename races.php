<?php
    require 'inc/racesFunctions.php';
    require 'inc/navFunctions.php';
?>
<script src="scripts/js/jsonSearch.js"></script>
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

        ?>
        <div class="search-section">
            <input type="text" class="live-search" placeholder="Search classes...">

            <?php
            if (isset($_GET['raceId']) && is_numeric($_GET['raceId'])) {
                displayRace($_GET['raceId']);
                displayRaces();
            } else {
                displayRaces();
            }
            ?>
        </div>
        <?php
        displayFooter();
        ?>
    </body>
</html>