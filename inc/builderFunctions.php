<?php
function getCharacter($characterId)
{
    $db = dbConnect();

    $sql = "SELECT * FROM characters WHERE characterId =" . $characterId;

    $resourse = $db->query($sql) or die($db->error);

    $character = $resourse->fetch_assoc();

    return $character;
}

function getCharacterSkills($characterId)
{
    $conn = dbConnect();
    $skills = [];

    $sql = "SELECT s.skillId, s.skillName, s.abilityName, cs.proficiency
            FROM skills s
            LEFT JOIN characterskills cs ON s.skillId = cs.skillId AND cs.characterId = ?
            ORDER BY s.skillId";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('getCharacterSkills prepare failed: ' . $conn->error);
        return $skills;
    }

    $stmt->bind_param("i", $characterId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $skills;
}

function getAbilityModifier($score)
{
    return floor(($score - 10) / 2);
}

function getProficiencyBonus($level)
{
    if ($level >= 17)
        return 6;
    if ($level >= 13)
        return 5;
    if ($level >= 9)
        return 4;
    if ($level >= 5)
        return 3;
    return 2;
}

function calculateSkillModifier($character, $skill, $proficiencyLevel)
{
    $abilityScore = $character[$skill['abilityName']];
    $abilityMod = getAbilityModifier($abilityScore);
    $proficiencyBonus = getProficiencyBonus($character['level']);

    // proficiencyLevel is one of 'none', 'proficient', 'expertise'
    $profMultiplier = 0;
    if ($proficiencyLevel === 'proficient') {
        $profMultiplier = 1;
    } elseif ($proficiencyLevel === 'expertise') {
        $profMultiplier = 2;
    }

    return $abilityMod + ($proficiencyBonus * $profMultiplier);
}

function calculateSavingThrowModifier($character, $ability, $proficiencyBonus)
{
    $abilityScore = isset($character[$ability]) ? $character[$ability] : 10;
    $abilityMod = getAbilityModifier($abilityScore);
    $savingThrowProficiencies = explode(',', $character['savingThrowProficiencies'] ?? '');
    $isProficient = in_array($ability, $savingThrowProficiencies);
    return $abilityMod + ($isProficient ? $proficiencyBonus : 0);
}

function handleSkillUpdates($characterId)
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['skills'])) {
        $conn = dbConnect();

        foreach ($_POST['skills'] as $skillId => $proficiency) {
            $skillId = intval($skillId);
            $proficiency = in_array($proficiency, ['none', 'proficient', 'expertise']) ? $proficiency : 'none';

            // Insert or update the skill entry
            $stmt = $conn->prepare("INSERT INTO characterskills (characterId, skillId, proficiency)
                                    VALUES (?, ?, ?)
                                    ON DUPLICATE KEY UPDATE proficiency = VALUES(proficiency)");
            if ($stmt) {
                $stmt->bind_param("iis", $characterId, $skillId, $proficiency);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "<p>❌ Skill update failed for skill ID {$skillId}: " . $conn->error . "</p>";
            }
        }

        echo "<p>✅ Skills updated successfully.</p>";
    }
}


function homeTabBuilder($characterId)
{
    //TODO:
    //make everything work with updatebuilder.js

    $classes = getClassesFromJson();
    $races = getRacesFromJson();
    $raceFluff = getRacesFluffFromJson();
    $character = getCharacter($characterId);
    ?>
    <style>
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>

    <h2>Create Your Character</h2>

    <!-- Tab Links -->
    <div class="tab-links">
        <a href="#general">General</a>
        <a href="#class">Class</a>
        <a href="#race">Race</a>
        <a href="#abilities">Abilities</a>
        <a href="#submit">Submit Character</a>
    </div>

    <form action="builder.php?characterId=<?php echo $character['characterId']; ?>" method="POST">
        <!-- General Tab -->
        <div id="general" class="tab-content">
            <label for="characterName">Character Name:</label>
            <input type="text" id="characterName" name="characterName" value="<?php echo $character['characterName']; ?>"
                required><br>

            <label for="age">Age:</label>
            <input type="text" id="characterAge" name="age" value="<?php echo $character['characterAge']; ?>" required><br>

            <label for="level">Level:</label>
            <input type="text" id="level" name="level" value="<?php echo $character['level']; ?>" required><br>

            <label>Alignment:</label>
            <select name="alignment" id="alignment" required>
                <option value="">--Choose Option--</option>
                <option value="Chaotic Neutral" <?php if ($character['alignment'] == 'Chaotic Neutral')
                    echo 'selected'; ?>>
                    Chaotic Neutral</option>
                <option value="Chaotic Good" <?php if ($character['alignment'] == 'Chaotic Good')
                    echo 'selected'; ?>>Chaotic
                    Good</option>
                <option value="Chaotic Evil" <?php if ($character['alignment'] == 'Chaotic Evil')
                    echo 'selected'; ?>>Chaotic
                    Evil</option>
                <option value="Lawful Neutral" <?php if ($character['alignment'] == 'Lawful Neutral')
                    echo 'selected'; ?>>
                    Lawful Neutral</option>
                <option value="Lawful Good" <?php if ($character['alignment'] == 'Lawful Good')
                    echo 'selected'; ?>>Lawful
                    Good</option>
                <option value="Lawful Evil" <?php if ($character['alignment'] == 'Lawful Evil')
                    echo 'selected'; ?>>Lawful
                    Evil</option>
                <option value="Neutral" <?php if ($character['alignment'] == 'Neutral')
                    echo 'selected'; ?>>Neutral</option>
                <option value="Neutral Good" <?php if ($character['alignment'] == 'Neutral Good')
                    echo 'selected'; ?>>Neutral
                    Good</option>
                <option value="Neutral Evil" <?php if ($character['alignment'] == 'Neutral Evil')
                    echo 'selected'; ?>>Neutral
                    Evil</option>
            </select><br>
        </div>

        <!-- Class Tab -->
        <div id="class" class="tab-content search-section">
            <label for="characterClass">Classes:</label><br>

            <!-- Live search input -->
            <input type="text" class="live-search" placeholder="Search classes...">

            <?php
            foreach ($classes as $index => $class) {
                $name = htmlspecialchars($class['name']);
                $source = isset($class['source']) ? htmlspecialchars($class['source']) : '';
                ?>
                <div class="filter-item" data-name="<?php echo strtolower($name); ?>"
                    data-source="<?php echo strtolower($source); ?>">
                    <p><?php echo $name; ?></p>
                    <input type="hidden" name="characterClass" class="class-radio" value="<?php echo $index; ?>" 
                    <?php if ($character['classId'] == $index) echo 'checked'; ?>>
                    <button type="button"
                        onclick="showClassModal(<?php echo $index; ?>, '<?php echo addslashes($name); ?>', 'More info about <?php echo addslashes($name); ?> will be loaded here.')">
                        More Info
                    </button>
                </div>
                <?php
            }
            ?>
            <div id="class-modal" class="modal" hidden>
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <div id="modal-class-info">
                        <!-- Content will be injected dynamically -->
                    </div>
                    <button id="confirm-selection" type="button">Select This Class</button>
                </div>
            </div>
            <div id="modal-overlay" class="overlay" hidden></div>
        </div>
        <script src="scripts/js/jsonSearch.js"></script>

        <!-- Race Tab -->
        <div id="race" class="tab-content search-section">
            <label for="characterRace">Race:</label><br>

            <input type="text" class="live-search" placeholder="Search races...">

            <?php
            foreach ($races as $index => $race) {
                $name = htmlspecialchars($race['name']);
                $source = isset($race['source']) ? htmlspecialchars($race['source']) : '';
                ?>
                <div class="filter-item" data-name="<?php echo strtolower($name); ?>" data-source="<?php echo strtolower($source); ?>">
                    <p><?php echo $name; ?></p>
                    <input type="hidden" name="characterRace" class="race-radio" value="<?php echo $index; ?>"
                        <?php if ($character['raceId'] == $index) echo 'checked'; ?>>
                    <button type="button"
                        onclick="showRaceModal(<?php echo $index; ?>, '<?php echo addslashes($name); ?>', '<?php echo addslashes(getFluffSnippet($race['entries'] ?? [])); ?>')">
                        More Info
                    </button>
                </div>
                <?php
            }
            ?>
            <div id="race-modal" class="modal" hidden>
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <div id="modal-race-info">
                        <!-- Injected content -->
                    </div>
                    <button id="confirm-race-selection" type="button">Select This Race</button>
                </div>
            </div>
            <div id="modal-overlay" class="overlay" hidden></div>

        </div>

        <!-- Abilities Tab -->
        <div id="abilities" class="tab-content">
            <h3>Ability Scores</h3>
            <button type="button" onclick="rollAbilities()">Roll Ability Scores</button><br><br>

            <?php
            $abilities = ['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'];
            foreach ($abilities as $ability):
                $value = $character[$ability] ?? '';
                ?>
                <label for="<?php echo $ability; ?>"><?php echo ucfirst($ability); ?>:</label>
                <input type="number" name="<?php echo $ability; ?>" id="<?php echo $ability; ?>" class="ability-score"
                    data-field="<?php echo $ability; ?>" value="<?php echo $value; ?>" required><br>
            <?php endforeach; ?>
        </div>


        <!-- Submit Tab -->
        <div id="submit" class="tab-content">
            <label>Submit:</label>
            <br>
            <button type="submit">Create Character</button>
        </div>

        <!-- update builder connection -->
        <?php $userId = json_encode($_SESSION['user']['id']); ?>
        <script>
            const userId = <?php echo $userId; ?>;
        </script>
    </form>
    <?php
}
