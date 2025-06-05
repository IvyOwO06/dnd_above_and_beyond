<?php
require_once 'functions.php';

function getItemsFromJson() {
    $jsonFile = 'scripts/js/json/items.json'; // Adjust path to your JSON file
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


function getCharacterCurrency($characterId) {
    $conn = dbConnect();
    if (!$conn) {
        error_log('getCharacterCurrency: Failed to connect to database');
        return null;
    }

    $characterId = $conn->real_escape_string($characterId);
    $query = "SELECT cp, sp, ep, gp, pp FROM charactercurrency WHERE characterId = '$characterId'";
    $result = $conn->query($query);

    if (!$result) {
        error_log('getCharacterCurrency query failed: ' . $conn->error);
        $conn->close();
        return null;
    }

    $currency = $result->num_rows > 0 ? $result->fetch_assoc() : ['cp' => 0, 'sp' => 0, 'ep' => 0, 'gp' => 0, 'pp' => 0];
    $result->free();
    $conn->close();
    return $currency;
}

function updateCharacterCurrency($characterId, $cp, $sp, $ep, $gp, $pp) {
    $conn = dbConnect();
    if (!$conn) {
        error_log('updateCharacterCurrency: Failed to connect to database');
        return false;
    }

    $characterId = $conn->real_escape_string($characterId);
    $cp = max(0, $conn->real_escape_string($cp)); // Ensure non-negative
    $sp = max(0, $conn->real_escape_string($sp));
    $ep = max(0, $conn->real_escape_string($ep));
    $gp = max(0, $conn->real_escape_string($gp));
    $pp = max(0, $conn->real_escape_string($pp));

    // Check if currency record exists
    $query = "SELECT currencyId FROM charactercurrency WHERE characterId = '$characterId'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Update existing record
        $query = "UPDATE charactercurrency SET cp = '$cp', sp = '$sp', ep = '$ep', gp = '$gp', pp = '$pp' WHERE characterId = '$characterId'";
    } else {
        // Insert new record
        $query = "INSERT INTO charactercurrency (characterId, cp, sp, ep, gp, pp) VALUES ('$characterId', '$cp', '$sp', '$ep', '$gp', '$pp')";
    }

    $success = $conn->query($query);
    if (!$success) {
        error_log('updateCharacterCurrency query failed: ' . $conn->error);
    }

    $conn->close();
    return $success;
}