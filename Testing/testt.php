<?php
include 'inc/backgroundFunctions.php';

// Get all backgrounds from the database
$backgroundsQuery = getDBConnection()->query("SELECT * FROM backgrounds");
$backgrounds = $backgroundsQuery->fetchAll(PDO::FETCH_ASSOC);

$backgroundId = isset($_POST['backgroundId']) ? (int)$_POST['backgroundId'] : 1; // Default to Acolyte (id 1)

// Get background details
$traits = getBackgroundTraits($backgroundId);
$choices = getBackgroundChoices($backgroundId);
$languages = getLanguages();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Character Background</title>
</head>
<body>

<h2>Select Your Background</h2>

<!-- Background Selection Dropdown -->
<form method="POST" id="backgroundForm">
    <select name="backgroundId" id="backgroundSelect">
        <?php foreach ($backgrounds as $background): ?>
            <option value="<?php echo $background['backgroundId']; ?>" <?php if ($background['backgroundId'] == $backgroundId) echo 'selected'; ?>>
                <?php echo htmlspecialchars($background['backgroundName']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Select Background</button>
</form>

<div id="backgroundDetails">
    <h3>Background Traits:</h3>
    <ul>
        <?php foreach ($traits as $trait): ?>
            <li><strong><?php echo htmlspecialchars($trait['backgroundTraitName']); ?>:</strong> <?php echo htmlspecialchars($trait['backgroundTraitDescription']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<div id="languageChoicesContainer">
    <?php if ($backgroundId == 1): // Acolyte background, has language choices ?>
        <h3>Select Languages:</h3>
        <select class="language-select">
            <?php foreach ($languages as $lang): ?>
                <option value="<?php echo htmlspecialchars($lang['languageName']); ?>"><?php echo htmlspecialchars($lang['languageName']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <select class="language-select">
            <?php foreach ($languages as $lang): ?>
                <option value="<?php echo htmlspecialchars($lang['languageName']); ?>"><?php echo htmlspecialchars($lang['languageName']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <button id="saveLanguages">Save Languages</button>
    <?php else: ?>
        <p>No languages available for this background.</p>
    <?php endif; ?>
</div>

<div id="selectedLanguages">
    <h3>Selected Languages:</h3>
    <ul id="languagesList"></ul>
</div>

<script>
document.getElementById("saveLanguages").addEventListener("click", function() {
    // Get the selected languages
    const selectedLanguages = Array.from(document.querySelectorAll(".language-select")).map(select => select.value);

    // Display the selected languages
    const languagesList = document.getElementById("languagesList");
    languagesList.innerHTML = "";
    selectedLanguages.forEach(language => {
        const li = document.createElement("li");
        li.textContent = language;
        languagesList.appendChild(li);
    });

    // Send languages to server for saving
    fetch("saveLanguages.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            characterId: 1, // This should be dynamically set
            languages: selectedLanguages
        })
    }).then(response => response.json())
      .then(data => {
          alert("Languages saved!");
      });
});
</script>

</body>
</html>
