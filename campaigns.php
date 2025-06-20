<?php

require 'inc/navFunctions.php';
require 'inc/campaignFunctions.php';
$userId = $_GET['userId'];
loggedInCheck();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ඞ</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/campaign.css">
    </head>
    <body>
        <?php
        displayHeader();
        if($_SESSION['user']['id'] == $userId)
        {
            ?>
            <a href="dm_notes.php">Dm Notes</a>
            <form method="POST" action="campaigns?userId=<?php echo $userId ?>" class="campaignform">
                <label for="campaignName">Create Campaign</label><br>

                <input type="text" id="campaignName" name="campaignName" placeholder="Campaign Name" required><br><br>

                <textarea id="description" name="description" placeholder="Description..." rows="2" cols="15"></textarea><br><br>

                <button type="submit">Create</button>
            </form>
            <?php
            createcampaign($userId);
        }
        displaycampaigns($userId);
        displayFooter();
        ?>
    </body>
</html>