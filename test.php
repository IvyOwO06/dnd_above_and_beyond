<?php
require 'inc/classesFunctions.php';
require 'inc/racesFunctions.php';
require 'inc/navFunctions.php';
function homeTabBuilder()
{
    $classes = getClasses();
    $races = getRaces();
    ?>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>

    

    <h2>Create Your Character</h2>

    <!-- Tab Links -->
    <div class="tab-links">
        <a href="#general">General</a>
        <a href="#class">Class</a>
        <a href="#race">Race</a>
    </div>

    <form action="builder.php" method="POST">
        <!-- General Tab -->
        <div id="general" class="tab-content">
            <label for="characterName">Character Name:</label>
            <input type="text" id="characterName" name="characterName" required><br>

            <label for="age">Age:</label>
            <input type="text" id="characterAge" name="age" required><br>

            <label for="level">Level:</label>
            <input type="text" id="characterLevel" name="level" required><br>

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
                <option>Neutral Evil</option>
            </select><br>
        </div>

        <!-- Class Tab -->
        <div id="class" class="tab-content">
            <label for="characterClass">Classes:</label><br>
            <?php  
                foreach($classes as $class) {
                    ?>
                    <div>
                        <p><?php echo $class['className']; ?></p>
                        <input type="radio" name="characterClass" value="<?php echo $class['classId']; ?>">

                        <button 
                            type="button" 
                            onclick="toggleInfo('class', <?php echo $class['classId']; ?>)" 
                            id="class-arrow-<?php echo $class['classId']; ?>">
                            ▶
                        </button>

                        <div id="class-info-<?php echo $class['classId']; ?>" hidden>
                            <p><?php echo $class['classShortInformation'] ?></p>
                            <a href="classes.php?classId=<?php echo $class['classId']; ?>" target="_blank">Read more</a>
                        </div><br>
                    </div>
                    <?php
                }
            ?>
        </div>

        <!-- Race Tab -->
        <div id="race" class="tab-content">
            <label for="characterRace">Race:</label><br>
            <?php  
                foreach($races as $race) {
                    ?>
                    <div>
                        <p><?php echo $race['raceName']; ?></p>
                        <input type="radio" name="characterRace" value="<?php echo $race['raceId']; ?>">

                        <button 
                            type="button" 
                            onclick="toggleInfo('race', <?php echo $race['raceId']; ?>)" 
                            id="race-arrow-<?php echo $race['raceId']; ?>">
                            ▶
                        </button>

                        <div id="race-info-<?php echo $race['raceId']; ?>" hidden>
                            <p><?php echo $race['raceShortInformation'] ?></p>
                            <a href="">Read more</a>
                        </div><br>
                    </div>
                    <?php
                }
            ?>
        </div>

        <br>
        <button type="submit">Create Character</button>
    </form>
    <?php
}
?>
<!DOCTYPE html>
<html>
    <body>
        <?php
homeTabBuilder();
        ?>
    </body>
</html>