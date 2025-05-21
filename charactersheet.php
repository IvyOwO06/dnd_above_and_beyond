<?php
require 'inc/functions.php';
require 'inc/builderFunctions.php';
require 'inc/racesFunctions.php';

if (!isset($_GET['characterId']) || !is_numeric($_GET['characterId'])) {
    die("Character ID not provided.");
}

$characterId = intval($_GET['characterId']);
$character = getCharacter($characterId);

if (!$character) {
    die("Character not found.");
}

// Get race data
$race = getRaceFromJson($character['raceId']);
$raceFluff = getRacesFluffFromJson();
$raceFluffEntry = null;

foreach ($raceFluff as $fluff) {
    if ($fluff['name'] === $race['name'] && $fluff['source'] === $race['source']) {
        $raceFluffEntry = $fluff;
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($character['characterName']); ?> - Character Sheet</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($character['characterName']); ?></h1>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($character['characterAge']); ?></p>
    <p><strong>Level:</strong> <?php echo htmlspecialchars($character['level']); ?></p>
    <p><strong>Alignment:</strong> <?php echo htmlspecialchars($character['alignment']); ?></p>

    <h2>Race: <?php echo htmlspecialchars($race['name']); ?></h2>
    <p><strong>Source:</strong> <?php echo htmlspecialchars($race['source']); ?></p>

    <?php if ($raceFluffEntry && isset($raceFluffEntry['entries'])): ?>
        <h3>Lore</h3>
        <?php foreach ($raceFluffEntry['entries'] as $entry): ?>
            <?php if (is_string($entry)): ?>
                <p><?php echo htmlspecialchars($entry); ?></p>
            <?php elseif (is_array($entry) && isset($entry['entries'])): ?>
                <?php foreach ($entry['entries'] as $sub): ?>
                    <p><?php echo htmlspecialchars($sub); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p><em>No additional lore available for this race.</em></p>
    <?php endif; ?>

 <h2>Ability Scores</h2>
<ul>
    <li><strong>STR:</strong> <?php echo $character['strength']; ?></li>
    <li><strong>DEX:</strong> <?php echo $character['dexterity']; ?></li>
    <li><strong>CON:</strong> <?php echo $character['constitution']; ?></li>
    <li><strong>INT:</strong> <?php echo $character['intelligence']; ?></li>
    <li><strong>WIS:</strong> <?php echo $character['wisdom']; ?></li>
    <li><strong>CHA:</strong> <?php echo $character['charisma']; ?></li>
</ul>

<?php
foreach ($skills as $skill) {
    $profLevel = $characterSkills[$skill['skillId']] ?? 'none';
    $mod = calculateSkillModifier($character, $skill, $profLevel);
    $modSign = $mod >= 0 ? "+" : "";
    echo "<p>" . htmlspecialchars($skill['skillName']) . ": <strong>{$modSign}{$mod}</strong> (" . ucfirst($profLevel) . ")</p>";
}
?>


</body>
</html>
