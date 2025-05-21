<?php

// Load class index and map to numeric IDs
function getClassesFromJson() {
    $indexJson = file_get_contents('js/json/class/index.json');
    $index = json_decode($indexJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding index.json: " . json_last_error_msg());
        return [];
    }

    $classes = [];
    $classId = 0;
    foreach ($index as $classKey => $filename) {
        $filePath = 'js/json/class/' . $filename;
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

// Load class fluff from JSON
function getClassesFluffFromJson() {
    $filePath = 'js/json/class/fluff-classes.json';
    if (!file_exists($filePath)) {
        error_log("Fluff file not found: $filePath");
        return [];
    }
    $json = file_get_contents($filePath);
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding fluff-classes.json: " . json_last_error_msg());
        return [];
    }
    return $data['classFluff'] ?? [];
}

// Get a single class by numeric ID
function getClassFromJson($classId) {
    $classes = getClassesFromJson();
    return $classes[$classId] ?? null;
}

// Display class list with optional search
function displayClasses() {
    $classes = getClassesFromJson();
    $search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
    $selectedClassId = isset($_GET['classId']) && is_numeric($_GET['classId']) ? (int)$_GET['classId'] : null;

    foreach ($classes as $index => $class) {
        // Skip selected class
        if ($selectedClassId !== null && $index === $selectedClassId) {
            continue;
        }

        // Skip if search is active and this class or source doesn't match
        if ($search) {
            $nameMatch = strpos(strtolower($class['name']), $search) !== false;
            $sourceMatch = isset($class['source']) && strpos(strtolower($class['source']), $search) !== false;
            if (!$nameMatch && !$sourceMatch) {
                continue;
            }
        }

        ?>
        <a href="?classId=<?php echo $index; ?>">
            <div>
                <h1><?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Source: <?php echo htmlspecialchars($class['source'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </a>
        <?php
    }
}

// Render nested entries
function renderEntries($entries, $depth = 2) {
    foreach ($entries as $entry) {
        if (is_string($entry)) {
            echo "<p>" . htmlspecialchars($entry, ENT_QUOTES, 'UTF-8') . "</p>";
        } elseif (is_array($entry)) {
            if (isset($entry['name'])) {
                echo "<h$depth>" . htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8') . "</h$depth>";
            }
            if (isset($entry['entries']) && is_array($entry['entries'])) {
                renderEntries($entry['entries'], min($depth + 1, 6));
            }
        }
    }
}

// Display selected class
function displayClass($classId) {
    $class = getClassFromJson($classId);
    if (!$class) {
        echo "<p>Class not found.</p>";
        return;
    }

    // Display core class info
    echo "<h1>" . htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8') . "</h1>";
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

    // Load fluff and find match
    $fluffEntries = getClassesFluffFromJson();
    $matchedFluff = null;

    foreach ($fluffEntries as $fluff) {
        if (
            isset($fluff['name'], $fluff['source']) &&
            $fluff['name'] === $class['name'] &&
            $fluff['source'] === $class['source']
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
        echo "<p><em>No fluff available for this version of the class.</em></p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        a div {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        a div h1 {
            margin: 0;
            font-size: 1.5em;
        }
        a div p {
            margin: 5px 0;
            color: #555;
        }
        .class-details {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
        }
        .info {
            color: #004085;
            background-color: #cce5ff;
            padding: 10px;
            border-radius: 4px;
        }
        h1, h2, h3, h4, h5, h6 {
            margin-top: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Class Viewer</h1>

    <!-- Search Form -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search classes..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>">
        <input type="submit" value="Search">
    </form>

    <?php
    $selectedClassId = isset($_GET['classId']) && is_numeric($_GET['classId']) ? (int)$_GET['classId'] : null;

    // Validate classId
    $classes = getClassesFromJson();
    if ($selectedClassId !== null && !isset($classes[$selectedClassId])) {
        $selectedClassId = null;
        echo "<p class='error'>Invalid class ID.</p>";
    }

    // Display selected class or prompt
    if ($selectedClassId !== null) {
        echo "<div class='class-details'>";
        displayClass($selectedClassId);
        echo "</div>";
    } else {
        echo "<p class='info'>Please select a class to view details.</p>";
    }

    // Display class list
    displayClasses();
    ?>
</body>
</html>