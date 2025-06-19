<?php
header('Content-Type: application/json');
include 'builderfunctions.php';

// Load JSON data
$json_data = json_decode(file_get_contents('classes.json'), true);
$class_data = null;
foreach ($json_data['class'] as $class) {
    if ($class['name'] === 'Bard' && $class['source'] === 'PHB') {
        $class_data = $class;
        break;
    }
}

// Get POST data
$character_id = $_POST['character_id'];
$new_level = (int)$_POST['new_level'];
$subclass = isset($_POST['subclass']) ? $_POST['subclass'] : null;

// Validate input
if (!$character_id || $new_level < 1 || $new_level > 20) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Fetch character (assuming builderfunctions.php has a query function)
$result = query("SELECT * FROM characters WHERE characterId = ?", [$character_id]);
$character = fetch($result); // Assuming fetch() gets the first row

if (!$character) {
    echo json_encode(['error' => 'Character not found']);
    exit;
}

// Check user ownership (assuming userId is in session or POST)
session_start();
if ($character['userId'] != $_SESSION['userId']) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Decode existing features
$features = json_decode($character['features'] ?? '[]', true);

// Get features up to new level
$new_features = [];
foreach ($json_data['classFeature'] as $feature) {
    if ($feature['className'] === 'Bard' && $feature['classSource'] === 'PHB' && $feature['level'] <= $new_level) {
        $new_features[] = $feature['name'];
    }
}

// Handle subclass features (e.g., College of Lore at level 3)
if ($new_level >= 3 && $subclass) {
    foreach ($json_data['subclass'] as $sub) {
        if ($sub['className'] === 'Bard' && $sub['shortName'] === $subclass && $sub['classSource'] === 'PHB') {
            foreach ($sub['subclassFeatures'] as $sub_feature) {
                $feature_level = (int)preg_replace('/.*\|(\d+)$/', '$1', $sub_feature);
                if ($feature_level <= $new_level) {
                    $feature_name = preg_replace('/\|.*/', '', $sub_feature);
                    $new_features[] = $feature_name;
                }
            }
            break;
        }
    }
}

// Update character (assuming update() function in builderfunctions.php)
update('characters', [
    'level' => $new_level,
    'subclass' => $subclass ?: $character['subclass'],
    'features' => json_encode(array_unique($new_features)),
    'cantrips_known' => $class_data['cantripProgression'][$new_level - 1],
    'spells_known' => $class_data['spellsKnownProgression'][$new_level - 1],
    'spell_slots' => json_encode($class_data['classTableGroups'][1]['rowsSpellProgression'][$new_level - 1])
], ['characterId' => $character_id]);

echo json_encode(['success' => true, 'features' => $new_features]);
?>