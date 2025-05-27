<?php
require 'inc/functions.php';
$db = dbConnect();

$field = $_POST['field'];
$value = $_POST['value'];
$userId = $_SESSION['user']['id'];

$fieldTypes = [
    'name' => 's',
    'age'  => 'i'
];

// Whitelist only valid fields to prevent SQL injection
$allowed_fields = ['name', 'age'];
if (!in_array($field, $allowed_fields)) {
    http_response_code(400);
    echo "Invalid field";
    exit;
}

// Prepare the SQL dynamically
$sql = "UPDATE characters SET `$field` = ? WHERE characterId = `$characterId` AND userId = `$userId`";
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

$type = $fieldTypes[$field];

$stmt->bind_param($type, $value);

// Execute
if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Execute error: " . $stmt->error;
}

$stmt->close();