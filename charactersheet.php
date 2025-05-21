<?php
require 'inc/functions.php';
require 'inc/builderFunctions.php';
require 'inc/racesFunctions.php';
require 'inc/inventoryFunctions.php';

if (!isset($_GET['characterId']) || !is_numeric($_GET['characterId'])) {
    die("Character ID not provided.");
}

$characterId = intval($_GET['characterId']);
$character = getCharacter($characterId);

if (!$character) {
    die("Character not found.");
}

// Get race data
$race = getRaceFromJson($character['raceId']);
$raceFluff = getRacesFluffFromJson();
$raceFluffEntry = null;

foreach ($raceFluff as $fluff) {
    if ($fluff['name'] === $race['name'] && $fluff['source'] === $race['source']) {
        $raceFluffEntry = $fluff;
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($character['characterName']); ?> - Character Sheet</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($character['characterName']); ?></h1>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($character['characterAge']); ?></p>
    <p><strong>Level:</strong> <?php echo htmlspecialchars($character['level']); ?></p>
    <p><strong>Alignment:</strong> <?php echo htmlspecialchars($character['alignment']); ?></p>

    <h2>Race: <?php echo htmlspecialchars($race['name']); ?></h2>
    <p><strong>Source:</strong> <?php echo htmlspecialchars($race['source']); ?></p>

    <?php if ($raceFluffEntry && isset($raceFluffEntry['entries'])): ?>
        <h3>Lore</h3>
        <?php foreach ($raceFluffEntry['entries'] as $entry): ?>
            <?php if (is_string($entry)): ?>
                <p><?php echo htmlspecialchars($entry); ?></p>
            <?php elseif (is_array($entry) && isset($entry['entries'])): ?>
                <?php foreach ($entry['entries'] as $sub): ?>
                    <p><?php echo htmlspecialchars($sub); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p><em>No additional lore available for this race.</em></p>
    <?php endif; ?>

 <h2>Ability Scores</h2>
<ul>
    <li><strong>STR:</strong> <?php echo $character['strength']; ?></li>
    <li><strong>DEX:</strong> <?php echo $character['dexterity']; ?></li>
    <li><strong>CON:</strong> <?php echo $character['constitution']; ?></li>
    <li><strong>INT:</strong> <?php echo $character['intelligence']; ?></li>
    <li><strong>WIS:</strong> <?php echo $character['wisdom']; ?></li>
    <li><strong>CHA:</strong> <?php echo $character['charisma']; ?></li>
</ul>

<?php
$items = getItemsFromJson();
?>

<h2>Inventory</h2>

<!-- Current Inventory -->
<h3>Your Inventory</h3>
<div id="character-inventory">
    <?php
    $inventory = getCharacterInventory($characterId);
    if (empty($inventory)) {
        echo "<p>No items in inventory.</p>";
    } else {
        echo "<ul>";
        foreach ($inventory as $invItem) {
            $itemId = htmlspecialchars($invItem['itemName']);
            $quantity = htmlspecialchars($invItem['quantity']);
            echo "<li>$itemId (Qty: $quantity) <button onclick=\"removeItem('$itemId')\">Remove</button></li>";
        }
        echo "</ul>";
    }
    ?>
</div>

<!-- Item Search and List -->
<h3>Add Items</h3>
<input type="text" id="item-search" placeholder="Search items..." onkeyup="searchItems()">
<select id="sort-items" onchange="sortItems()">
    <option value="name-asc">Name (A-Z)</option>
    <option value="name-desc">Name (Z-A)</option>
    <option value="type-asc">Type (A-Z)</option>
    <option value="type-desc">Type (Z-A)</option>
</select>
<div id="item-list">
    <!-- Populated by JavaScript -->
</div>

<script>
// Pass PHP items to JavaScript
const items = <?php echo json_encode($items); ?>;

function searchItems() {
    const searchTerm = document.getElementById('item-search').value.toLowerCase();
    const filteredItems = items.filter(item => 
        item.name.toLowerCase().includes(searchTerm) || 
        (item.type && item.type.toLowerCase().includes(searchTerm))
    );
    displayItems(filteredItems);
}

function sortItems() {
    const sortValue = document.getElementById('sort-items').value;
    const sortedItems = [...items].sort((a, b) => {
        if (sortValue === 'name-asc') return a.name.localeCompare(b.name);
        if (sortValue === 'name-desc') return b.name.localeCompare(a.name);
        if (sortValue === 'type-asc') return (a.type || '').localeCompare(b.type || '');
        if (sortValue === 'type-desc') return (b.type || '').localeCompare(a.type || '');
    });
    displayItems(sortedItems);
}

function displayItems(items) {
    const itemList = document.getElementById('item-list');
    itemList.innerHTML = '';
    items.forEach(item => {
        const div = document.createElement('div');
        div.innerHTML = `${item.name} (${item.type || 'Unknown'}) <button onclick="addItem('${item.name}')">Add</button>`;
        itemList.appendChild(div);
    });
}


function addItem(itemName) {
    const characterId = <?php echo json_encode($characterId); ?>;
    fetch('add_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ characterId: characterId, itemName: itemName, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item added to inventory!');
            // Refresh inventory display
            location.reload(); // Simple approach; you can optimize with dynamic DOM update
        } else {
            alert('Error adding item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add item.');
    });
}


// Initial display
sortItems();
</script>




</body>
</html>
