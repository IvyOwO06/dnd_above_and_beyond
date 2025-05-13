<?php
function handleCharacterCreation()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['characterName'])) {
        $name = trim($_POST['characterName']);
        $age = intval($_POST['age']);
        $classId = intval($_POST['characterClass']);
        $raceId = intval($_POST['characterRace']);
        $alignment = trim($_POST['alignment']);
        $level = intval($_POST['level']);
        $userId = $_SESSION['user']['id'];

        if (!empty($name) && $classId > 0 && $raceId > 0) {
            $conn = dbConnect();

            $stmt = $conn->prepare("INSERT INTO characters (characterName, characterAge, classId, raceId, alignment, level, userId) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                die("SQL prepare failed: " . $conn->error); // ← This will show the actual reason
            }

            $stmt->bind_param("siiisii", $name, $age, $classId, $raceId, $alignment, $level, $userId);
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
        <input type="text" id="characterName" name="characterName" required>
        <br>

        <label for="age">Age:</label>
        <input type="text" id="characterAge" name="age" required>
        <br>

        <label for="level">Level:</label>
        <input type="text" id="characterLevel" name="level" required>
        <br>

        <label for="characterClass">Classes:</label>
        <br>
        <?php  
            foreach($classes as $class) {
                ?>
                <div>
                    <p><?php echo $class['className']; ?></p>
                    <input type="radio" name="characterClass" value="<?php echo $class['classId']; ?>">

                     <button 
                    type="button" 
                    onclick="toggleInfo('class', <?php echo $class['classId']; ?>)" 
                    id="arrow"
                    >
                    ▶
                    </button>

                    <div id="class-info-<?php echo $class['classId']; ?>" hidden>
                        <p><?php echo $class['classShortInformation'] ?></p>
                        <a href="classes.php?classId=<?php echo $class['classId']; ?>" target="_blank">Read more</a>
                    </div>
                    <br>
                </div>
                <?php
            }
        ?>
        <br>

        <label for="characterRace">Race:</label>
        <br>
        <?php  
            foreach($races as $race) {
                ?>
                <div>
                    <p><?php echo $race['raceName']; ?></p>
                    <input type="radio" name="characterRace" value="<?php echo $race['raceId']; ?>">

                     <button 
                    type="button" 
                    onclick="toggleInfo('race', <?php echo $race['raceId']; ?>)" 
                    id="arrow"
                    >
                    ▶
                    </button>

                    <div id="race-info-<?php echo $race['raceId']; ?>" hidden>
                        <p><?php echo $race['raceShortInformation'] ?></p>
                        <a href="">Read more</a>
                    </div>
                    <br>
                </div>
                <?php
            }
        ?>
        <br>

        <label>Alignment:</label>
        <select name="alignment" required>
            <option>--Choose Option--</option>
            <option>Chaotic Neutral</option>
            <option>Chaotic Good</option>
            <option>Chaotic Evil</option>
            <option>Lawful Neutral</option>
            <option>Lawful Good</option>
            <option>Lawful Evil</option>
            <option>Neutral</option>
            <option>Neutral Good</option>
            <option>Nautural Evil</option>
        </select>
        <br>

        <button type="submit">Create Character</button>
    </form>
    <?php
}
?>