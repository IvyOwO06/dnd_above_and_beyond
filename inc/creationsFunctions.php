<?php

require_once 'functions.php';

function getCharacters($userId)
{
    $db = dbConnect();

    $sql = 'SELECT * FROM characters WHERE userId =' . $userId;

    $resource = $db->query($sql) or die($db->error);

    $characters = $resource->fetch_all(MYSQLI_ASSOC);

    return $characters;
}

function displayCharacters($userId)
{
    $characters = getCharacters($userId);

    foreach($characters as $character)
    {
        ?>
        <div>
            <h1><?php echo $character['characterName']; ?></h1>
            <div>
                <?php
                if($_GET['userId'] == $_SESSION['user']['id'])
                {
                ?>
                <a href="builder.php?characterId=<?php echo $character['characterId'] ?>">Edit</a>
                <a href="characterSheet.php?characterId=<?php echo $character['characterId']; ?>">View Sheet</a>
                <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
}