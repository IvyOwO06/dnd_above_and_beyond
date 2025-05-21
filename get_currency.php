<?php
require_once 'inc/inventoryFunctions.php';

$characterId = $_GET['characterId'] ?? null;

if (!$characterId || !is_numeric($characterId)) {
    echo json_encode(['cp' => 0, 'sp' => 0, 'ep' => 0, 'gp' => 0, 'pp' => 0]);
    exit;
}

$currency = getCharacterCurrency($characterId);
echo json_encode($currency);