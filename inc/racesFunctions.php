<?php
require_once 'functions.php';

function getRacesFromJson() {
    $json = file_get_contents('js/json/races/races.json');
    $data = json_decode($json, true);

    return $data['races'][0]['race']; // returns the array of races
}

function getRaceFromJson($raceId) {
    $races = getRacesFromJson();

    return $races[$raceId] ?? null;
}

function displayRaces() {
    $races = getRacesFromJson();
    $selectedRaceId = isset($_GET['raceId']) && is_numeric($_GET['raceId']) ? (int) $_GET['raceId'] : null;

    foreach ($races as $index => $race) {
        if ($selectedRaceId !== null && $index === $selectedRaceId) {
            continue;
        }

        ?>
        <a href="?raceId=<?php echo $index; ?>">
            <div>
                <h1><?php echo htmlspecialchars($race['name']); ?></h1>
                <p><?php echo htmlspecialchars($race['source']); ?>, page <?php echo $race['page']; ?></p>
            </div>
        </a>
        <?php
    }
}

function displayRace($raceId) {
    $race = getRaceFromJson($raceId);
    if (!$race) {
        echo "<p>Race not found.</p>";
        return;
    }

    ?>
    <h1><?php echo htmlspecialchars($race['name']); ?></h1>
    <p><strong>Source:</strong> <?php echo htmlspecialchars($race['source']); ?>, Page <?php echo $race['page']; ?></p>
    <?php
    if (isset($race['entries'])) {
        foreach ($race['entries'] as $entry) {
            echo "<h2>" . htmlspecialchars($entry['name']) . "</h2>";
            foreach ($entry['entries'] as $text) {
                echo "<p>" . htmlspecialchars($text) . "</p>";
            }
        }
    }
}
