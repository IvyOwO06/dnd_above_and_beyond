<?php
function getCharacter($characterId)
{
    $db = dbConnect();

    $sql = "SELECT * FROM characters WHERE characterId =" . $characterId;

    $resourse = $db->query($sql) or die($db->error);

    $character = $resourse->fetch_assoc();

    return $character;
}

    
function getCharacterSkills($characterId) {
    $conn = dbConnect();
    $skills = [];

    $sql = "SELECT s.skillId, s.skillName, cs.proficiency
            FROM skills s
            LEFT JOIN characterskills cs ON s.skillId = cs.skillId AND cs.characterId = ?
            ORDER BY s.skillId";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $characterId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }

    return $skills;
}


function handleCharacterCreation()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['characterName']) && isset($_GET['characterId'])) {
        $characterId = intval($_GET['characterId']);
        $name = trim($_POST['characterName']);
        $age = intval($_POST['age']);
        $classId = intval($_POST['characterClass']);
        $raceId = intval($_POST['characterRace']);
        $alignment = trim($_POST['alignment']);
        $level = intval($_POST['level']);
        $userId = $_SESSION['user']['id'];
        $str = intval($_POST['strength']);
        $dex = intval($_POST['dexterity']);
        $con = intval($_POST['constitution']);
        $int = intval($_POST['intelligence']);
        $wis = intval($_POST['wisdom']);
        $cha = intval($_POST['charisma']);


        if (!empty($name) && $classId > 0 && $raceId > 0 && $characterId > 0) {
            $conn = dbConnect();

            $stmt = $conn->prepare(" UPDATE characters SET characterName = ?, characterAge = ?, classId = ?, raceId = ?, alignment = ?, level = ?, strength = ?, dexterity = ?, constitution = ?, intelligence = ?, wisdom = ?, charisma = ? WHERE characterId = ? AND userId = ? ");

            if (!$stmt) {
                // Show the SQL error and stop the script
                die("❌ Prepare failed: " . $conn->error);
            }

                $stmt->bind_param("siiisiiiiiiiii", $name, $age, $classId, $raceId, $alignment, $level,
                $str, $dex, $con, $int, $wis, $cha,
                $characterId, $userId);                $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                echo "<p>✅ Character <strong>" . htmlspecialchars($name) . "</strong> updated successfully!</p>";
            } else {
                echo "<p>❌ Failed to update character.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>❗ Please fill out all fields correctly.</p>";
        }
    }
}



function homeTabBuilder($characterId)
{
    //TODO:
    //make it so that the character submit tab only apears when all the needed things are filled in

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
        <a href="#skills">Skills</a>
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
            <input type="text" id="characterLevel" name="level" value="<?php echo $character['level']; ?>" required><br>

            <label>Alignment:</label>
            <select name="alignment" required>
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
        <div id="class" class="tab-content">
            <label for="characterClass">Classes:</label><br>
            <?php
            foreach ($classes as $index => $class) {
                ?>
                <div>
                    <p><?php echo htmlspecialchars($class['name']); ?></p>
                    <input type="radio" name="characterClass" value="<?php echo $index; ?>" <?php if ($character['raceId'] == $index)
                            echo 'checked'; ?>>

                    <button type="button" onclick="toggleInfo('class', <?php echo $index; ?>)"
                        id="class-arrow-<?php echo $index; ?>">
                        ▶
                    </button>

                    <div id="class-info-<?php echo $index; ?>" hidden>
                        <p><?php echo $index; ?></p>
                        <a href="classes.php?classId=<?php echo $index ?>">Read more</a>
                    </div><br>
                </div>
                <?php
            }
            ?>
        </div>

        <!-- Race Tab -->
        <div id="race" class="tab-content">
            <label for="characterRace">Race:</label><br>
            <?php foreach ($races as $index => $race): ?>
                <div>
                    <div>
                        <p><?php echo htmlspecialchars($race['name']); ?></p>
                        <input type="radio" name="characterRace" value="<?php echo $index; ?>" <?php if ($character['raceId'] == $index)
                            echo 'checked'; ?>>
                    </div>

                    <button type="button" onclick="toggleInfo('race', <?php echo $index; ?>)"
                        id="race-arrow-<?php echo $index; ?>">
                        ▶
                    </button>

                    <div id="race-info-<?php echo $index; ?>" hidden>
                        <p><?php
                        // Pass the entries array to getFluffSnippet
                        // Adjust 'entries' to whatever key contains the snippet array
                        echo htmlspecialchars(getFluffSnippet($race['entries'] ?? []));
                        ?></p>
                        <a href="races.php?raceId=<?php echo $index ?>">Read more</a>
                    </div><br>
                </div>
            <?php endforeach; ?>
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
                <input type="number" name="<?php echo $ability; ?>" class="ability-score" value="<?php echo $value; ?>" required><br>
            <?php endforeach; ?>
        </div>

        <!-- Skills Tab -->
        <div id="skills" class="tab-content">
            <h3>Skills</h3>
            <?php
            $skills = getCharacterSkills($characterId);
            foreach ($skills as $skill):
                $skillName = $skill['skillName'];
                $skillId = $skill['skillId'];
                $proficiency = $skill['proficiency'] ?? 'none';
            ?>
                <label for="skill_<?php echo $skillId; ?>"><?php echo htmlspecialchars($skillName); ?>:</label>
                <select name="skills[<?php echo $skillId; ?>]" id="skill_<?php echo $skillId; ?>">
                    <option value="none" <?php if ($proficiency === 'none') echo 'selected'; ?>>None</option>
                    <option value="proficient" <?php if ($proficiency === 'proficient') echo 'selected'; ?>>Proficient</option>
                    <option value="expertise" <?php if ($proficiency === 'expertise') echo 'selected'; ?>>Expertise</option>
                </select><br>
            <?php endforeach; ?>
        </div>

        <!-- Submit Tab -->
        <div id="submit" class="tab-content">
            <label>Submit:</label>
            <br>
            <button type="submit">Create Character</button>
        </div>
    </form>
    <?php
}
