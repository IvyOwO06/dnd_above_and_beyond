<?php

require_once 'functions.php';

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
function displayCharacters($userId)
{
    $userId = filter_var($userId, FILTER_VALIDATE_INT);
    if ($userId === false) {
        die('Invalid user ID');
    }

    $characters = getCharacters($userId);
    ?>
    <div class="character-list">
        <?php if (empty($characters)): ?>
            <p class="no-characters">No characters found. Create one to start your adventure!</p>
        <?php else: ?>
            <?php foreach ($characters as $character): ?>
                <div class="character-card">
                
                    <h2 class="character-name"><?php echo htmlspecialchars($character['characterName']); ?></h2>
                    <?php if (isset($_GET['userId'], $_SESSION['user']['id']) && $_GET['userId'] == $_SESSION['user']['id']): ?>
                        <div class="character-actions">
                            <a href="builder.php?characterId=<?php echo $character['characterId']; ?>" class="action-button edit-button">Edit</a>
                            <a href="characterSheet.php?characterId=<?php echo $character['characterId']; ?>" class="action-button view-button">View Sheet</a>
                        </div>
                        <div class="character-image-container">
                        <img src="<?php echo $character['characterImage'] ? htmlspecialchars($character['characterImage']) : 'images/default_character.png'; ?>" 
                             alt="<?php echo htmlspecialchars($character['characterName']); ?>'s portrait" 
                             class="character-image">
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php
}