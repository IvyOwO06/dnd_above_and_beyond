<?php

require 'inc/navFunctions.php';
require 'inc/campaignFunctions.php';
$userId = $_GET['userId'];

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ඞ</title>
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <?php
        displayHeader();
        displaycampaigns($userId);
        displayFooter();
        ?>
    </body>
</html>