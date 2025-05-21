<?php
require_once 'functions.php';

function getItemsFromJson() {
    $jsonFile = 'js/json/items.json'; // Adjust path to your JSON file
    if (!file_exists($jsonFile)) {
        return [];
    }
    $jsonData = file_get_contents($jsonFile);
    $items = json_decode($jsonData, true);
    return $items['item'] ?? []; // Adjust based on JSON structure
}

function getCharacterInventory($characterId) {
    $conn = dbConnect();
    if (!$conn) {
        error_log('getCharacterInventory: Failed to connect to database');
        return [];
    }

    $characterId = $conn->real_escape_string($characterId);
    $query = "SELECT * FROM characterinventory WHERE characterId = '$characterId'";
    $result = $conn->query($query);

    if (!$result) {
        error_log('getCharacterInventory query failed: ' . $conn->error);
        $conn->close();
        return [];
    }

    $inventory = [];
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }

    $result->free();
    $conn->close();
    return $inventory;
}
    

function addItemToInventory($characterId, $itemName, $quantity = 1) {
    $conn = dbConnect();
    if (!$conn) {
        error_log('addItemToInventory: Failed to connect to database');
        return false;
    }

    $characterId = $conn->real_escape_string($characterId);
    $itemName = $conn->real_escape_string($itemName);
    $quantity = $conn->real_escape_string($quantity);

    // Check if item exists
    $query = "SELECT inventoryId, quantity FROM characterinventory WHERE characterId = '$characterId' AND itemName = '$itemName'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        $query = "UPDATE characterinventory SET quantity = '$newQuantity' WHERE inventoryId = '{$row['inventoryId']}'";
    } else {
        $query = "INSERT INTO characterinventory (characterId, itemName, quantity, equipped) VALUES ('$characterId', '$itemName', '$quantity', 0)";
    }

    $success = $conn->query($query);
    $conn->close();
    return $success;
}