<?php
require 'inc/functions.php';
require 'inc/builderFunctions.php';
require 'inc/racesFunctions.php';
require 'inc/inventoryFunctions.php';
require_once 'inc/skillFunctions.php';

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

<h2>Proficiency Bonus</h2>
<p><strong>Proficiency Bonus:</strong> +<?php echo getProficiencyBonus($character['level']); ?></p>

<h2>Ability Scores</h2>
<ul>
    <li><strong>STR:</strong> <?php echo $character['strength'] . ' (' . getAbilityModifier($character['strength']) . ')'; ?></li>
    <li><strong>DEX:</strong> <?php echo $character['dexterity'] . ' (' . getAbilityModifier($character['dexterity']) . ')'; ?></li>
    <li><strong>CON:</strong> <?php echo $character['constitution'] . ' (' . getAbilityModifier($character['constitution']) . ')'; ?></li>
    <li><strong>INT:</strong> <?php echo $character['intelligence'] . ' (' . getAbilityModifier($character['intelligence']) . ')'; ?></li>
    <li><strong>WIS:</strong> <?php echo $character['wisdom'] . ' (' . getAbilityModifier($character['wisdom']) . ')'; ?></li>
    <li><strong>CHA:</strong> <?php echo $character['charisma'] . ' (' . getAbilityModifier($character['charisma']) . ')'; ?></li>
</ul>
<h2>Proficiency Bonus</h2>
<p><strong>Proficiency Bonus:</strong> +<?php echo getProficiencyBonus($character['level']); ?></p>

<h2>Skills</h2>
<ul id="skills-list">
    <?php
    $skills = getCharacterSkills($characterId);
    if (empty($skills)) {
        echo "<li>No skills available.</li>";
    } else {
        foreach ($skills as $skill) {
            $skillName = htmlspecialchars($skill['skillName'] ?? 'Unknown Skill');
            $abilityName = htmlspecialchars($skill['abilityName'] ?? 'Unknown');
            $proficiencyLevel = $skill['proficiency'] ?? 'none';
            $skillModifier = calculateSkillModifier($character, $skill, $proficiencyLevel);
            $modifierDisplay = $skillModifier >= 0 ? "+$skillModifier" : $skillModifier;
            $modifierClass = $skillModifier >= 0 ? 'positive' : 'negative';
            ?>
            <li data-skill-id="<?php echo $skill['skillId']; ?>">
                <strong><?php echo $skillName; ?>:</strong>
                <span class="<?php echo $modifierClass; ?>"><?php echo $modifierDisplay; ?></span>
                (<?php echo $abilityName; ?>)
                <select onchange="updateSkillProficiency(<?php echo $skill['skillId']; ?>, this.value)">
                    <option value="none" <?php if ($proficiencyLevel === 'none') echo 'selected'; ?>>None</option>
                    <option value="proficient" <?php if ($proficiencyLevel === 'proficient') echo 'selected'; ?>>Proficient</option>
                    <option value="expertise" <?php if ($proficiencyLevel === 'expertise') echo 'selected'; ?>>Expertise</option>
                </select>
            </li>
            <?php
        }
    }
    ?>
</ul>

<h2>Saving Throws</h2>
<ul>
    <?php
    $proficiencyBonus = getProficiencyBonus($character['level']);
    $abilities = ['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'];
    foreach ($abilities as $ability) {
        $savingThrowModifier = calculateSavingThrowModifier($character, $ability, $proficiencyBonus);
        $modifierDisplay = $savingThrowModifier >= 0 ? "+$savingThrowModifier" : $savingThrowModifier;
        $modifierClass = $savingThrowModifier >= 0 ? 'positive' : 'negative';
        $isProficient = in_array($ability, explode(',', $character['savingThrowProficiencies'] ?? '')) ? 'Proficient' : 'None';
        echo "<li><strong>" . ucfirst($ability) . ":</strong> <span class='$modifierClass'>$modifierDisplay</span> ($isProficient)</li>";
    }
    ?>
</ul>
<?php
$items = getItemsFromJson();
?>

<h2>Currency</h2>
<div id="character-currency">
    <?php
    $currency = getCharacterCurrency($characterId);
    if (!$currency) {
        echo "<p>Error loading currency.</p>";
    } else {
        echo "<p><strong>Platinum (pp):</strong> {$currency['pp']}</p>";
        echo "<p><strong>Gold (gp):</strong> {$currency['gp']}</p>";
        echo "<p><strong>Electrum (ep):</strong> {$currency['ep']}</p>";
        echo "<p><strong>Silver (sp):</strong> {$currency['sp']}</p>";
        echo "<p><strong>Copper (cp):</strong> {$currency['cp']}</p>";
    }
    ?>
</div>

<h3>Manage Currency</h3>
<form id="currency-form">
    <label>Platinum (pp): <input type="number" id="pp" min="0" value="0"></label><br>
    <label>Gold (gp): <input type="number" id="gp" min="0" value="0"></label><br>
    <label>Electrum (ep): <input type="number" id="ep" min="0" value="0"></label><br>
    <label>Silver (sp): <input type="number" id="sp" min="0" value="0"></label><br>
    <label>Copper (cp): <input type="number" id="cp" min="0" value="0"></label><br>
    <button type="button" onclick="updateCurrency('add')">Add Currency</button>
    <button type="button" onclick="updateCurrency('remove')">Remove Currency</button>
</form>

<script>function updateSkillProficiency(skillId, proficiency) {
    const characterId = <?php echo json_encode($characterId); ?>;
    fetch('inc/skillFunctions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'updateSkillProficiency', characterId, skillId, proficiency })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Skill proficiency updated!');
            // Fetch the updated modifier for this skill
            fetch(`inc/skillFunctions.php?action=getSkillModifier&characterId=${characterId}&skillId=${skillId}`)
                .then(response => response.json())
                .then(data => {
                    const modifier = data.modifier;
                    const modifierDisplay = modifier >= 0 ? `+${modifier}` : modifier;
                    const modifierClass = modifier >= 0 ? 'positive' : 'negative';
                    // Find the specific skill's <li> element
                    const skillItem = document.querySelector(`#skills-list li[data-skill-id="${skillId}"]`);
                    if (skillItem) {
                        // Update the modifier and proficiency dropdown
                        const modifierSpan = skillItem.querySelector('span');
                        modifierSpan.textContent = modifierDisplay;
                        modifierSpan.className = modifierClass;
                        const select = skillItem.querySelector('select');
                        select.value = proficiency;
                    }
                })
                .catch(error => {
                    console.error('Error fetching modifier:', error);
                    alert('Failed to update skill modifier.');
                });
        } else {
            alert('Error updating skill proficiency: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update skill proficiency.');
    });
}

    function updateCurrency(action) {
    const characterId = <?php echo json_encode($characterId); ?>;
    const cp = parseInt(document.getElementById('cp').value) || 0;
    const sp = parseInt(document.getElementById('sp').value) || 0;
    const ep = parseInt(document.getElementById('ep').value) || 0;
    const gp = parseInt(document.getElementById('gp').value) || 0;
    const pp = parseInt(document.getElementById('pp').value) || 0;

    fetch('update_currency.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ characterId, cp, sp, ep, gp, pp, action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Currency updated!');
            // Refresh currency display
            fetch('get_currency.php?characterId=' + characterId)
                .then(response => response.json())
                .then(currency => {
                    const currencyDiv = document.getElementById('character-currency');
                    currencyDiv.innerHTML = `
                        <p><strong>Platinum (pp):</strong> ${currency.pp}</p>
                        <p><strong>Gold (gp):</strong> ${currency.gp}</p>
                        <p><strong>Electrum (ep):</strong> ${currency.ep}</p>
                        <p><strong>Silver (sp):</strong> ${currency.sp}</p>
                        <p><strong>Copper (cp):</strong> ${currency.cp}</p>
                    `;
                    // Reset form
                    document.getElementById('currency-form').reset();
                });
        } else {
            alert('Error updating currency: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update currency.');
    });
}
</script>

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
