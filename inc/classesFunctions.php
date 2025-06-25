<?php

require_once "functions.php";

////classes////
// get all classes
function getClassesFromJson() {
    $indexJson = file_get_contents('scripts/js/json/class/index.json');
    $index = json_decode($indexJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding index.json: " . json_last_error_msg());
        return [];
    }

    $classes = [];
    $classId = 0;
    foreach ($index as $classKey => $filename) {
        $filePath = 'scripts/js/json/class/' . $filename;
        if (!file_exists($filePath)) {
            error_log("Class file not found: $filePath");
            continue;
        }
        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Error decoding $filePath: " . json_last_error_msg());
            continue;
        }
        if (isset($data['class'][0])) {
            $classes[$classId] = $data['class'][0];
            $classes[$classId]['_key'] = $classKey; // Store original key for fluff matching
            $classId++;
        }
    }
    return $classes;
}  

// display all classes
function displayClasses() {
    $classes = getClassesFromJson();
    $search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
    $selectedClassId = isset($_GET['classId']) && is_numeric($_GET['classId']) ? (int)$_GET['classId'] : null;

    foreach ($classes as $index => $class) {
        // Skip selected class
        if ($selectedClassId !== null && $index === $selectedClassId) {
            continue;
        }

        ?>
        <div class="filter-item"
            data-name="<?php echo strtolower(htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8')); ?>"
            data-source="<?php echo strtolower(htmlspecialchars($class['source'], ENT_QUOTES, 'UTF-8')); ?>">
            <a href="?classId=<?php echo $index; ?>">
                <h1><?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Source: <?php echo htmlspecialchars($class['source'], ENT_QUOTES, 'UTF-8'); ?></p>
            </a>
        </div>

        <?php
    }
}

function getClassFluffByKey($classKey) {
    $indexPath = 'scripts/js/json/class/fluff-index.json';
    if (!file_exists($indexPath)) {
        error_log("Fluff index not found: $indexPath");
        return null;
    }

    $index = json_decode(file_get_contents($indexPath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding fluff-index.json: " . json_last_error_msg());
        return null;
    }

    if (!isset($index[$classKey])) {
        error_log("No fluff file mapped for class key: $classKey");
        return null;
    }

    $fluffFilePath = 'scripts/js/json/class/' . $index[$classKey];
    if (!file_exists($fluffFilePath)) {
        error_log("Fluff file not found: $fluffFilePath");
        return null;
    }

    $data = json_decode(file_get_contents($fluffFilePath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding $fluffFilePath: " . json_last_error_msg());
        return null;
    }

    return $data['classFluff'] ?? null;
}

// get a singular class
function getClassFromJson($classId) {
    $classes = getClassesFromJson();
    return $classes[$classId] ?? null;
}

function renderEntries($entries, $depth = 2) {
    foreach ($entries as $entry) {
        if (is_string($entry)) {
            echo "<p>" . htmlspecialchars(stripJsonTags($entry), ENT_QUOTES, 'UTF-8') . "</p>";
        } elseif (is_array($entry)) {
            $type = $entry['type'] ?? null;
            $name = $entry['name'] ?? null;

            if ($name) {
                echo "<h$depth>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</h$depth>";
            }

            if (!empty($entry['entries'])) {
                renderEntries($entry['entries'], min($depth + 1, 6));
            }

            if ($type === 'quote' && !empty($entry['entries'])) {
                echo "<blockquote>";
                foreach ($entry['entries'] as $quoteLine) {
                    echo "<p>" . htmlspecialchars($quoteLine, ENT_QUOTES, 'UTF-8') . "</p>";
                }
                if (isset($entry['by'])) {
                    echo "<footer>&mdash; " . htmlspecialchars($entry['by'], ENT_QUOTES, 'UTF-8') . "</footer>";
                }
                echo "</blockquote>";
            }
        }
    }
}

// display a singular class
function displayClass($classId) {
    $class = getClassFromJson($classId);
    if (!$class) {
        echo "<p>Class not found.</p>";
        return;
    }

    // Display core class info
    echo "<a href='classes.php'><h1>" . htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8') . "</h1></a>";
    echo "<p><strong>Source:</strong> " . htmlspecialchars($class['source'], ENT_QUOTES, 'UTF-8') . ", Page " . htmlspecialchars($class['page'], ENT_QUOTES, 'UTF-8') . "</p>";

    if (isset($class['hd']['number'], $class['hd']['faces'])) {
        echo "<p><strong>Hit Dice:</strong> {$class['hd']['number']}d{$class['hd']['faces']}</p>";
    }
    if (isset($class['proficiency']) && is_array($class['proficiency'])) {
        $proficiencies = array_map(function($prof) {
            return htmlspecialchars($prof, ENT_QUOTES, 'UTF-8');
        }, $class['proficiency']);
        echo "<p><strong>Proficiencies:</strong> " . implode(', ', $proficiencies) . "</p>";
    }
    if (isset($class['spellcastingAbility'])) {
        $spellAbility = htmlspecialchars(strtoupper($class['spellcastingAbility']), ENT_QUOTES, 'UTF-8');
        echo "<p><strong>Spellcasting Ability:</strong> $spellAbility</p>";
    }

    // Class entries (features and mechanics)
    if (isset($class['entries'])) {
        foreach ($class['entries'] as $entry) {
            if (isset($entry['name'])) {
                echo "<h2>" . htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8') . "</h2>";
            }
            if (isset($entry['entries'])) {
                renderEntries($entry['entries']);
            }
        }
    }

    // Load fluff and find match (by name only for flexibility)
    $matchedFluff = null;
    if (isset($class['_key'])) {
        $fluffEntries = getClassFluffByKey($class['_key']);
        if ($fluffEntries) {
            foreach ($fluffEntries as $fluff) {
                if (isset($fluff['name']) && $fluff['name'] === $class['name']) {
                    $matchedFluff = $fluff;
                    break;
                }
            }
        }
    }


    // Display fluff
    if ($matchedFluff && isset($matchedFluff['entries'])) {
        echo "<h2>Lore</h2>";
        renderEntries($matchedFluff['entries']);
    } else {
        echo "<p><em>No fluff available for this version of the class.</em></p>";
    }
}
?>