<?php
require_once 'functions.php';

function getRacesFromJson() {
    $json = file_get_contents('js/json/races/races.json');
    $data = json_decode($json, true);

    return $data['races'][0]['race']; // returns the array of races
}

//TODO:
//need to make it so the fluff displays with the races
function getRacesFluffFromJson() {
    $json = file_get_contents('js/json/races/fluff-races.json');
    $data = json_decode($json, true);

    return $data['raceFluff']; // returns the array of raceFluff
}

function getRaceFromJson($raceId) {
    $races = getRacesFromJson();

    return $races[$raceId] ?? null;
}

function displayRaces() {
    $races = getRacesFromJson();
    $search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
    $selectedRaceId = isset($_GET['raceId']) && is_numeric($_GET['raceId']);

    foreach ($races as $index => $race) {
        // Skip selected race
        if ($selectedRaceId !== null && $index === $selectedRaceId) {
            continue;
        }

        // Skip if search is active and this race or source doesn't match
        if ($search) {
            $nameMatch = strpos(strtolower($race['name']), $search) !== false;
            $sourceMatch = isset($race['source']) && strpos(strtolower($race['source']), $search) !== false;

            if (!$nameMatch && !$sourceMatch) {
                continue; // Skip if it matches neither name nor source
            }
        }


        ?>
        <a href="?raceId=<?php echo $index; ?>">
            <div>
                <h1><?php echo htmlspecialchars($race['name']); ?></h1>
                <p>Source: <?php echo htmlspecialchars($race['source']); ?></p>
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
