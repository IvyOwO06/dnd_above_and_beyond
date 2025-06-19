<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level Up Character</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Level Up Your Character</h1>
    <div id="character-info">
        <h2 id="char-name"></h2>
        <p>Current Level: <span id="char-level"></span></p>
        <p>Class: <span id="char-class"></span></p>
        <p>Subclass: <span id="char-subclass"></span></p>
        <p>Features: <span id="char-features"></span></p>
    </div>
    <div id="level-up-form">
        <label for="new-level">New Level:</label>
        <input type="number" id="new-level" min="1" max="20">
        <div id="subclass-selection" style="display: none;">
            <label for="subclass">Choose Subclass:</label>
            <select id="subclass">
                <option value="">Select Subclass</option>
            </select>
        </div>
        <button onclick="levelUp()">Level Up</button>
    </div>
    <script src="scripts/js/script.js"></script>

</body>
</html>