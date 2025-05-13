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
