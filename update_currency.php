<?php
require_once 'inc/inventoryFunctions.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$characterId = $input['characterId'] ?? null;
$cp = isset($input['cp']) ? (int)$input['cp'] : 0;
$sp = isset($input['sp']) ? (int)$input['sp'] : 0;
$ep = isset($input['ep']) ? (int)$input['ep'] : 0;
$gp = isset($input['gp']) ? (int)$input['gp'] : 0;
$pp = isset($input['pp']) ? (int)$input['pp'] : 0;
$action = $input['action'] ?? null;

$response = ['success' => false, 'message' => ''];

if (!$characterId || !is_numeric($characterId) || !in_array($action, ['add', 'remove'])) {
    $response['message'] = 'Invalid character ID or action.';
    echo json_encode($response);
    exit;
}

$currentCurrency = getCharacterCurrency($characterId);
if (!$currentCurrency) {
    $response['message'] = 'Failed to retrieve current currency.';
    echo json_encode($response);
    exit;
}

$newCp = $action === 'add' ? $currentCurrency['cp'] + $cp : $currentCurrency['cp'] - $cp;
$newSp = $action === 'add' ? $currentCurrency['sp'] + $sp : $currentCurrency['sp'] - $sp;
$newEp = $action === 'add' ? $currentCurrency['ep'] + $ep : $currentCurrency['ep'] - $ep;
$newGp = $action === 'add' ? $currentCurrency['gp'] + $gp : $currentCurrency['gp'] - $gp;
$newPp = $action === 'add' ? $currentCurrency['pp'] + $pp : $currentCurrency['pp'] - $pp;

if ($newCp < 0 || $newSp < 0 || $newEp < 0 || $newGp < 0 || $newPp < 0) {
    $response['message'] = 'Cannot reduce currency below zero.';
    echo json_encode($response);
    exit;
}

if (updateCharacterCurrency($characterId, $newCp, $newSp, $newEp, $newGp, $newPp)) {
    $response['success'] = true;
} else {
    $response['message'] = 'Failed to update currency.';
}

echo json_encode($response);