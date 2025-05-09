<?php
function displayClassesSelection()
{
    $classes = getClasses();

    foreach ($classes as $class) {
        ?>
        <option value="<?php echo $class['classId'] ?>"><?php echo $class['className'] ?></option>
        <?php
    }
}

function displayRacesSelection()
{
    $races = getRaces();

    foreach ($races as $race) {
        ?>
        <option value="<?php echo $race['raceId'] ?>"><?php echo $race['raceName'] ?></option>
        <?php
    }
}

function handleCharacterCreation()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['characterName'])) {
        $name = trim($_POST['characterName']);
        $classId = intval($_POST['characterClass']);
        $raceId = intval($_POST['characterRace']);
        $userId = $_SESSION['user']['id'] ?? 1;

        if (!empty($name) && $classId > 0 && $raceId > 0) {
            $conn = dbConnect();

            $stmt = $conn->prepare("INSERT INTO characters (characterName, classId, raceId, userId) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $name, $classId, $raceId, $userId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p>✅ Character <strong>" . htmlspecialchars($name) . "</strong> created successfully!</p>";
            } else {
                echo "<p>❌ Failed to create character.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>❗ Please fill out all fields correctly.</p>";
        }
    }
}


function homeTabBuilder()
{
    $classes = getClasses();
    $races = getRaces();
    ?>
    <h2>Create Your Character</h2>
    <form action="builder.php" method="POST">
        <label for="characterName">Character Name:</label>
        <input type="text" id="characterName" name="characterName" required><br><br>

        <label for="characterClass">Class:</label>
        <select id="characterClass" name="characterClass" required>
            <option value="">--Select Class--</option>
            <?php displayClassesSelection(); ?>
        </select><br><br>
        <div id="classDescription" style="margin-bottom: 1em;"></div>

        <label for="characterRace">Race:</label>
        <select id="characterRace" name="characterRace" required>
            <option value="">--Select Race--</option>
            <?php displayRacesSelection(); ?>
        </select><br><br>
        <div id="raceDescription" style="margin-bottom: 1em;"></div>

        <button type="submit">Create Character</button>
    </form>
    <?php
}
?>