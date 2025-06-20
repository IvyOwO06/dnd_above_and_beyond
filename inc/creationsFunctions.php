<?php

require_once 'functions.php';
require_once 'classesFunctions.php';

$conn = dbConnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['delete_character_id'])) {
    $charId = intval($_POST['delete_character_id']);
    $userId = $_SESSION['user']['id'] ?? null;

    if (deleteCharacter($conn, $charId, $userId)) {
    header("Location: index.php");
    } else {
        echo "❌ You can’t delete this character.";
        exit;
    }
}


function getCharacters($userId)
{
    $db = dbConnect();

    $sql = "SELECT * FROM characters WHERE userId = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $characters = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $db->close();

    return $characters;
}

function displayCharacters($userId, $limit = null)
{
    $userId = filter_var($userId, FILTER_VALIDATE_INT);
    if ($userId === false) {
        die('Invalid user ID');
    }

    $characters = getCharacters($userId);

    if ($limit !== null) {
        $characters = array_slice($characters, 0, $limit);
    }
    ?>
    <div class="character-list">
        <?php if (empty($characters)): ?>
            <p class="no-characters">No characters found. Forge a new legend in the annals of adventure!</p>
        <?php else: ?>
            <?php foreach ($characters as $character):
                $classId = $character['classId'];
                $class = getClassFromJson($classId);
            ?>
                <div class="character-card" data-class="<?php echo htmlspecialchars($character['classId'] ?? 'unknown'); ?>">
                    <div class="rune-glow"></div>
                    <h2 class="character-name"><?php echo htmlspecialchars($character['characterName']); ?></h2>
                    <div class="character-class-badge"><?php echo htmlspecialchars($class['name'] ?? 'No Class!'); ?></div>
                    <div class="character-image-container">
                        <img src="<?php echo $character['characterImage'] ? htmlspecialchars($character['characterImage']) : 'https://media.istockphoto.com/id/673584626/vector/wizard.jpg?s=612x612&w=0&k=20&c=byLcsx_78OpIzs7dH6hbV7_K7aR60rmP7IZ3KHwW8-U='; ?>" 
                             alt="<?php echo htmlspecialchars($character['characterName']); ?>'s portrait" 
                             class="character-image">
                    </div>
                    <?php if (isset($_SESSION['user']['id']) && $userId == $_SESSION['user']['id']): ?>
                        <div class="character-actions">
                            <a href="builder.php?characterId=<?php echo $character['characterId']; ?>" class="action-button edit-button">Edit</a>
                            <a href="characterSheet.php?characterId=<?php echo $character['characterId']; ?>" class="action-button view-button">View Sheet</a>
                            <a href="characterprofile.php?characterId=<?php echo $character['characterId']; ?>" class="action-button view-button">View Profile</a>
                            <form method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="delete_character_id" value="<?php echo $character['characterId']; ?>">
                                <button type="submit" name="delete" value="1" class="delete-character">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php
}

function deleteCharacter($conn, $characterId, $userId) {
    // Make sure $characterId and $userId are valid integers
    $characterId = intval($characterId);
    $userId = intval($userId);

    // Verify that the character belongs to the user
    $sql = "SELECT userId FROM characters WHERE characterId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $characterId);
    $stmt->execute();
    $stmt->bind_result($ownerId);
    if (!$stmt->fetch() || $ownerId !== $userId) {
        $stmt->close();
        return false; // Not authorized or character not found
    }
    $stmt->close();

    // Delete character
    $sql = "DELETE FROM characters WHERE characterId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $characterId);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
