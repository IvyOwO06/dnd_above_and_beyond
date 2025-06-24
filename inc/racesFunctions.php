<?php
require_once 'functions.php';

function getRacesFromJson() {
    $json = file_get_contents('scripts/js/json/races/races.json');
    $data = json_decode($json, true);

    return $data['races'][0]['race']; // returns the array of races
}

function getRacesFluffFromJson() {
    $json = file_get_contents('scripts/js/json/races/fluff-races.json');
    $data = json_decode($json, true);
        
    return $data['raceFluff']; // returns the array of raceFluff
}

function getFluffSnippet(array $entries, int $maxParts = 3): string {
    $snippetParts = [];

    foreach ($entries as $entry) {
        if (is_string($entry) && trim($entry) !== '') {
            $snippetParts[] = $entry;
        } elseif (is_array($entry) && isset($entry['entries']) && is_array($entry['entries'])) {
            foreach ($entry['entries'] as $subentry) {
                if (is_string($subentry) && trim($subentry) !== '') {
                    $snippetParts[] = $subentry;
                }
                if (count($snippetParts) >= $maxParts) {
                    break 2; // stop if we reached max parts
                }
            }
        }
        if (count($snippetParts) >= $maxParts) {
            break; // stop if we reached max parts
        }
    }

    // Join parts with space (or use "\n" or something else)
    $snippet = implode(' ', $snippetParts);

    return $snippet ?: ''; // fallback empty string if no snippet found
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
        <div class="filter-item"
            data-name="<?php echo strtolower(htmlspecialchars($race['name'], ENT_QUOTES, 'UTF-8')); ?>"
            data-source="<?php echo strtolower(htmlspecialchars($race['source'], ENT_QUOTES, 'UTF-8')); ?>">
            <a href="?raceId=<?php echo $index; ?>">
                <h1><?php echo htmlspecialchars($race['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Source: <?php echo htmlspecialchars($race['source'], ENT_QUOTES, 'UTF-8'); ?></p>
            </a>
        </div>
        <?php
    }
}


function displayRace($raceId) {
    $race = getRaceFromJson($raceId);
    if (!$race) {
        echo "<p>Race not found.</p>";
        return;
    }

    // Inline helper function to recursively render entries safely
    function renderEntries($entries, $depth = 2) {
        foreach ($entries as $entry) {
            if (is_string($entry)) {
                echo "<p>" . htmlspecialchars(stripJsonTags($entry), ENT_QUOTES, 'UTF-8') . "</p>";
            } elseif (is_array($entry)) {
                if (isset($entry['name'])) {
                    echo "<h$depth>" . htmlspecialchars($entry['name']) . "</h$depth>";
                }
                if (isset($entry['entries']) && is_array($entry['entries'])) {
                    renderEntries($entry['entries'], min($depth + 1, 6));
                }
            }
        }
    }

    // Display core race info
    echo "<h1>" . htmlspecialchars($race['name']) . "</h1>";
    echo "<p><strong>Source:</strong> " . htmlspecialchars($race['source']) . ", Page " . $race['page'] . "</p>";

    // Race entries (traits and mechanics)
    if (isset($race['entries'])) {
        foreach ($race['entries'] as $entry) {
            if (isset($entry['name'])) {
                echo "<h2>" . htmlspecialchars($entry['name']) . "</h2>";
            }
            if (isset($entry['entries'])) {
                renderEntries($entry['entries']);
            }
        }
    }

    // Load fluff and find match
    $fluffEntries = getRacesFluffFromJson();
    $matchedFluff = null;

    foreach ($fluffEntries as $fluff) {
        if (
            isset($fluff['name'], $fluff['source']) &&
            $fluff['name'] === $race['name'] &&
            $fluff['source'] === $race['source']
        ) {
            $matchedFluff = $fluff;
            break;
        }
    }

    // Display fluff
    if ($matchedFluff && isset($matchedFluff['entries'])) {
        echo "<h2>Lore</h2>";
        renderEntries($matchedFluff['entries']);
    } else {
        echo "<p><em>No fluff available for this version of the race.</em></p>";
    }
}
