<?php

require 'inc/navFunctions.php';
require 'inc/campaignFunctions.php';
$campaignId = $_GET['campaignId'];
loggedInCheck();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ඞ</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/campaignModal.css">
        <link rel="stylesheet" href="css/viewcampaign.css">
        <link rel="stylesheet" href="css/campaign.css">
    </head>
    <body>
        <?php
        displayHeader();
        echo '<div class="campaign-container">';
        displaycampaign($campaignId);
        echo '</div>';
        displayFooter();
        ?>
    </body>
</html>