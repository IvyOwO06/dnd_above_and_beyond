<?php
    require 'inc/classesFunctions.php';
    require 'inc/navFunctions.php';
?>
<script src="js/jsonSearch.js"></script>
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
            if (isset($_GET['classId']) && is_numeric($_GET['classId'])) {
                displayClass($_GET['classId']);
                displayClasses();
            } else {
                displayClasses();
            }
            ?>
        </div>

        <?php
        displayFooter();
        ?>
    </body>
</html>