<?php
include 'inc/functions.php'; // DB connection is in here
include 'inc/creationsFunctions.php'; // DB connection is in here
include 'inc/builderFunctions.php'; // DB connection is in here
require_once 'inc/navFunctions.php';


if (!isset($_GET['characterId']) || !is_numeric($_GET['characterId'])) {
    die("Character ID not provided.");
}

$characterId = intval($_GET['characterId']);
$character = getCharacter($characterId);

if (!$character) {
    die("Character not found.");
}

// Fetch character


if (!$character) {
    echo "Character does not exist.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($character['characterName']) ?>'s Profile</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/characterprofile.css">
    <?php
    displayHeader();
    ?>
</head>
<body>
    <div class="character-profile">
        <h1><?= htmlspecialchars($character['characterName']) ?></h1>
        <?php if ($character['characterImage']) : ?>
            <img src="<?= htmlspecialchars($character['characterImage']) ?>" alt="Character image">
        <?php endif; ?>

        <p><strong>Age:</strong> <?= $character['characterAge'] ?></p>
        <p><strong>Alignment:</strong> <?= htmlspecialchars($character['alignment']) ?></p>
        <p><strong>Level:</strong> <?= $character['level'] ?></p>

        <h2>Stats</h2>
        <ul>
            <li>STR: <?= $character['strength'] ?></li>
            <li>DEX: <?= $character['dexterity'] ?></li>
            <li>CON: <?= $character['constitution'] ?></li>
            <li>INT: <?= $character['intelligence'] ?></li>
            <li>WIS: <?= $character['wisdom'] ?></li>
            <li>CHA: <?= $character['charisma'] ?></li>
        </ul>

        <h2>Backstory</h2>
        <p><?= nl2br(htmlspecialchars($character['characterBackstory'])) ?></p>

        <h2>Personality</h2>
        <p><?= nl2br(htmlspecialchars($character['characterPersonality'])) ?></p>

        <h2>Health</h2>
        <p><?= $character['currentHP'] ?> / <?= $character['maxHP'] ?> HP</p>
    </div>
</body>
</html>
