<?php

require_once "functions.php";

////races////
// get all races
function getRaces(){
    $db = dbConnect();

    $sql = "SELECT * FROM race";

    $resourse = $db->query($sql) or die($db->error);

    $races = $resourse->fetch_all(MYSQLI_ASSOC);

    return $races;
}

// display all races
function displayRaces() {
    $races = getRaces();

    foreach($races as $race) {
        ?>
            <a href="?raceId=<?php echo $race['raceId']; ?>">
                <div>
                    <h1><?php echo $race['raceName'] ?></h1>
                    <p><?php echo$race['raceShortInformation'];  ?></p>
                </div>
            </a>
        <?php
    }
}

// get a singular race
function getRace($raceId) {
    $db = dbConnect();

    $sql = "SELECT * FROM race WHERE raceId =" . $raceId;

    $resourse = $db->query($sql) or die($db->error);

    $race = $resourse->fetch_assoc();

    return $race;
}

// display a singular race
function displayRace($raceId) {
    $race = getRace($raceId);

    ?>
        <h1><?php echo $race['raceName'] ?></h1>
        <p><?php echo nl2br($race['raceInformation']);  ?></p>
        <img src="<?php echo $race['raceImage'];?>">
    <?php
}
?>