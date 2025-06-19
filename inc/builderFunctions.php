<?php
function getCharacter($characterId)
{
    $db = dbConnect();

    $sql = "SELECT * FROM characters WHERE characterId =" . $characterId;

    $resourse = $db->query($sql) or die($db->error);

    $character = $resourse->fetch_assoc();

    return $character;
}

function handleImageUpload($characterId)
{
    if (!isset($_FILES['characterImage']) || $_FILES['characterImage']['error'] == UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'No file uploaded.'];
    }

    $file = $_FILES['characterImage'];
    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    $uploadDir = 'uploads/characters/';
    $fileName = $characterId . '_' . time() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;

    // Validate file
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only PNG, JPG, and JPEG are allowed.'];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds 2MB limit.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error: ' . $file['error']];
    }

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => false, 'message' => 'Failed to save file.'];
    }

    // Update database
    $db = dbConnect();
    $stmt = $db->prepare('UPDATE characters SET characterImage = ? WHERE characterId = ? AND userId = ?');
    $stmt->bind_param('sii', $filePath, $characterId, $_SESSION['user']['id']);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();

    if ($success) {
        return ['success' => true, 'message' => 'Image uploaded successfully!'];
    } else {
        unlink($filePath); // Delete file if DB update fails
        return ['success' => false, 'message' => 'Failed to update character image.'];
    }
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
    $classes = getClassesFromJson();
    $races = getRacesFromJson();
    $raceFluff = getRacesFluffFromJson();
    $character = getCharacter($characterId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['characterImage'])) {
        $uploadResult = handleImageUpload($characterId);
        if ($uploadResult['success']) {
            echo '<p class="success">' . $uploadResult['message'] . '</p>';
            $character = getCharacter($characterId);
        } else {
            echo '<p class="error">' . $uploadResult['message'] . '</p>';
        }
    }
    ?>

    <!-- Image Upload Form -->
    <form method="POST" enctype="multipart/form-data" class="character-image-form">
        <div class="form-group">
            <label for="characterImage">Character Image</label>
            <input type="file" id="characterImage" name="characterImage" accept="image/png,image/jpeg,image/jpg">
            <div id="image-preview" class="preview-image"></div>
            <?php if ($character['characterImage']): ?>
                <div class="current-image">
                    <img src="<?php echo htmlspecialchars($character['characterImage']); ?>" 
                         alt="Current character portrait" 
                         class="preview-image">
                    <p>Current Image</p>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit" class="action-button submit-button">Upload Image</button>
    </form>

    <h2>Create Your Character</h2>

    <!-- Tab Links -->
    <div class="tab-links">
        <a href="#general">General</a><br>
        <a href="#class">Class</a><br>
        <a href="#feats">Class Features</a><br>
        <a href="#race">Race</a><br>
        <a href="#abilities">Abilities</a><br>
    </div>

    <!-- Single Shared Overlay -->
    <div id="modal-overlay" class="overlay"></div>

    <form action="builder?characterId=<?php echo $character['characterId']; ?>" method="POST">
        <!-- General Tab -->
        <div id="general" class="tab-content">
            <label for="characterName">Character Name:</label>
            <input type="text" id="characterName" name="characterName" value="<?php echo htmlspecialchars($character['characterName']); ?>"
                   required><br>
            <label for="age">Age:</label>
            <input type="number" id="characterAge" name="age" value="<?php echo htmlspecialchars($character['characterAge']); ?>" required><br>
            <label>Alignment:</label>
            <select name="alignment" id="alignment" required>
                <option value="">--Choose Option--</option>
                <option value="Chaotic Neutral" <?php if ($character['alignment'] == 'Chaotic Neutral') echo 'selected'; ?>>Chaotic Neutral</option>
                <option value="Chaotic Good" <?php if ($character['alignment'] == 'Chaotic Good') echo 'selected'; ?>>Chaotic Good</option>
                <option value="Chaotic Evil" <?php if ($character['alignment'] == 'Chaotic Evil') echo 'selected'; ?>>Chaotic Evil</option>
                <option value="Lawful Neutral" <?php if ($character['alignment'] == 'Lawful Neutral') echo 'selected'; ?>>Lawful Neutral</option>
                <option value="Lawful Good" <?php if ($character['alignment'] == 'Lawful Good') echo 'selected'; ?>>Lawful Good</option>
                <option value="Lawful Evil" <?php if ($character['alignment'] == 'Lawful Evil') echo 'selected'; ?>>Lawful Evil</option>
                <option value="Neutral" <?php if ($character['alignment'] == 'Neutral') echo 'selected'; ?>>Neutral</option>
                <option value="Neutral Good" <?php if ($character['alignment'] == 'Neutral Good') echo 'selected'; ?>>Neutral Good</option>
                <option value="Neutral Evil" <?php if ($character['alignment'] == 'Neutral Evil') echo 'selected'; ?>>Neutral Evil</option>
            </select><br>
        </div>

        <!-- Class Tab -->
        <div id="class" class="tab-content search-section">
            <label for="characterClass">Classes:</label><br>
            <input type="text" class="live-search" placeholder="Search classes...">
            <?php
            $classId = $character['classId'];
            $class = getClassFromJson($classId);
            ?>
                <class>
                    <h2>Current Class</h2>
                    <p><?php echo $class['name'] ?></p>
                    <label for="level">Level:</label>
                    <select name="level" id="level">
                        <?php
                        for ($i = 1; $i <= 20; $i++)
                        {
                            ?>
                            <option value="<?php echo $i ?>" <?php if ($character['level'] == $i) echo 'selected'; ?>><?php echo $i ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <br>
                    <br>
                </class>

            <?php
            $indexPath = __DIR__ . "/../scripts/js/json/class/index.json";
            if (!file_exists($indexPath)) {
                die("Error: index.json not found at $indexPath.");
            }
            $indexMap = json_decode(file_get_contents($indexPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error: Could not decode index.json: " . json_last_error_msg());
            }

            foreach ($classes as $index => $class) {
                $name = htmlspecialchars($class['name']);
                $source = isset($class['source']) ? htmlspecialchars($class['source']) : '';
                $classKey = strtolower($name);

                $info = 'No feature available.';
                if (isset($indexMap[$classKey])) {
                    $classPath = __DIR__ . "/../scripts/js/json/class/" . $indexMap[$classKey];
                    if (file_exists($classPath)) {
                        $classData = json_decode(file_get_contents($classPath), true);
                        if ($classData && isset($classData['class'][0]) && isset($classData['classFeature'])) {
                            foreach ($classData['classFeature'] as $feature) {
                                if ($feature['level'] == 1 && strpos($feature['name'], 'Optional Rule') === false) {
                                    $info = $feature['name'] . ': ' . (is_array($feature['entries'][0]) ? 'See details.' : htmlspecialchars($feature['entries'][0], ENT_QUOTES));
                                    break;
                                }
                            }
                        }
                    }
                }

                echo "<!-- Debug: Processing $name (key: $classKey) -->";
                ?>
                <div class="filter-item" data-name="<?php echo strtolower($name); ?>" data-source="<?php echo strtolower($source); ?>">
                    <p><?php echo $name; ?></p>
                    <button type="button"
                            class="show-class-modal"
                            data-index="<?php echo $index; ?>"
                            data-name="<?php echo $name; ?>"
                            data-info="<?php echo htmlspecialchars($info, ENT_QUOTES); ?>">
                            Read more
                    </button>
                </div>
                <?php
            }
            ?>
            <div id="class-modal" class="modal" hidden>
                    <div class="modal-content">
                        <span class="close-button">x</span>
                        <div id="modal-class-info"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Class Features Tab -->
        <div id="feats" class="tab-content search-section">
            <?php
            $classId = $character['classId'];
            $class = getClassFromJson($classId);
            ?>
            <class>
                <h2>Current Class</h2>
                <p><?php echo $class['name'] ?></p>
                <label for="levels">Level:</label>
                <select name="levels" id="levels">
                    <?php
                    for ($i = 1; $i <= 20; $i++)
                    {
                        ?>
                        <option value="<?php echo $i ?>" <?php if ($character['level'] == $i) echo 'selected'; ?>><?php echo $i ?></option>
                        <?php
                    }
                    ?>
                </select>
                <br>
                <br>
            </class>
            <level>
                <?php
                $level = getClassLevel($classId);
                dd($level);
                ?>
            </level>
        </div>

        <!-- Race Tab -->
        <div id="race" class="tab-content search-section">
            <label for="characterRace">Race:</label><br>
            <input type="text" class="live-search" placeholder="Search races...">

            <?php
            $raceId = $character['raceId'];
            $race = getraceFromJson($raceId);
            ?>
            
            <race>
                <h2>Current Race</h2>
                <p><?php echo $race['name'] ?></p>
                <h2>Source</h2>
                <p><?php echo $race['source'] ?></p>
                <br>
                <br>
            </race>

            <?php
            foreach ($races as $index => $race) {
                $name = htmlspecialchars($race['name']);
                $source = isset($race['source']) ? htmlspecialchars($race['source']) : '';
                $fluffSnippet = htmlspecialchars(getFluffSnippet($race['entries'] ?? []), ENT_QUOTES);
                ?>
                <div class="filter-item" data-name="<?php echo strtolower($name); ?>" data-source="<?php echo strtolower($source); ?>">
                    <p><?php echo $name; ?></p>
                    <button type="button"
                            class="show-race-modal"
                            data-index="<?php echo $index; ?>"
                            data-name="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>"
                            data-info="<?php echo $fluffSnippet; ?>">
                            More Info
                    </button>
                </div>
                <?php
            }
            ?>
                <div id="race-modal" class="modal" hidden>
                    <div class="modal-content">
                        <span class="close-button">x</span>
                        <div id="modal-race-info"></div>
                    </div>
                </div>
            </div>
            
        <!-- Abilities Tab -->
        <div id="abilities" class="tab-content">
            <h3>Ability Scores</h3>
            <button type="button" onclick="rollAbilities()">Roll Ability Scores</button><br><br>
            <?php
            $abilities = ['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'];
            foreach ($abilities as $ability) {
                $value = $character[$ability] ?? '';
                ?>
                <label for="<?php echo $ability; ?>"><?php echo ucfirst($ability); ?>:</label>
                <input type="number" name="<?php echo $ability; ?>" id="<?php echo $ability; ?>" class="ability-score"
                       data-field="<?php echo $ability; ?>" value="<?php echo htmlspecialchars($value); ?>" required><br>
                <?php
            }
            ?>
        </div>
        <?php $userId = json_encode($_SESSION['user']['id']); ?>
        <script>
            const userId = <?php echo $userId; ?>;
        </script>
    </form>
    <?php
}
?>