<?php
require_once 'inc/inventoryFunctions.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$characterId = $input['characterId'] ?? null;
$itemName = $input['itemName'] ?? null;
$quantity = $input['quantity'] ?? 1;

$response = ['success' => false, 'message' => ''];

if (!$characterId || !is_numeric($characterId) || !$itemName) {
    $response['message'] = 'Invalid character ID or item name.';
    echo json_encode($response);
    exit;
}

if (addItemToInventory($characterId, $itemName, $quantity)) {
    $response['success'] = true;
} else {
    $response['message'] = 'Failed to add item.';
}

echo json_encode($response);