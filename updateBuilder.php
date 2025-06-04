<?php
require 'inc/functions.php';
$db = dbConnect();

$field = $_POST['field'];
$value = $_POST['value'];
$userId = $_POST['userId'];
$characterId = $_POST['characterId'];

$fieldTypes = [
    'userId' => 'i',
    'characterId' => 'i',
    'characterName' => 's',
    'characterAge' => 'i',
    'level' => 'i',
    'alignment' => 's',
    'strength' => 'i',
    'dexterity' => 'i',
    'constitution' => 'i',
    'intelligence' => 'i',
    'wisdom' => 'i',
    'charisma' => 'i',
    'classId' => 'i',
    'raceId' => 'i'
];

$allowed_fields = array_keys($fieldTypes); // cleaner than manually listing again

if (!in_array($field, $allowed_fields)) {
    http_response_code(400);
    echo "Invalid field";
    exit;
}

// Prepare the SQL dynamically
$sql = "UPDATE characters SET `$field` = ? WHERE characterId = ? AND userId = ?";
$stmt = $db->prepare($sql);

// ❗ Check if statement preparation worked
if (!$stmt) {
    http_response_code(500);
    echo "SQL prepare error: " . $db->error;
    exit;
}

// Choose data type: 's' for strings, 'i' for integers
if (!isset($fieldTypes[$field])) {
    http_response_code(400);
    echo "Invalid field";
    exit;
}

$type = $fieldTypes[$field] . 'ii';

$stmt->bind_param($type, $value, $characterId, $userId);

// Execute
if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Execute error: " . $stmt->error;
}

$stmt->close();