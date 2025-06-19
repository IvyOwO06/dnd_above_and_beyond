<?php
function getClassFeatures($characterId) 
{
    $character = getCharacter($characterId);
    $classId = intval($character['classId']);
    $class = getClassFromJson($classId);
    $characterLevel = intval($character['level']); // Ensure level is an integer

    $filePath = __DIR__ . '/../scripts/js/json/class/class-' . $class['name'] . '.json';
    if (!file_exists($filePath)) {
        error_log("Class file not found: $filePath");
        return [];
    }

    // Read JSON file content
    $json = file_get_contents($filePath);
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return [];
    }

    $features = [];

    // Check if 'classFeature' exists
    if (isset($data['classFeature']) && is_array($data['classFeature'])) {
        foreach ($data['classFeature'] as $feature) {
            // Check if the feature has a 'level' key, 'classSource' is 'PHB', and level is <= character level
            if (isset($feature['level']) && 
                isset($feature['classSource']) && $feature['classSource'] === 'PHB' &&
                $feature['level'] <= $characterLevel) {
                // Store feature data
                $features[] = [
                    'name' => $feature['name'] ?? 'No name',
                    'level' => $feature['level'],
                    'entries' => $feature['entries'] ?? []
                ];
            }
        }
    }

    return $features;
}
?>