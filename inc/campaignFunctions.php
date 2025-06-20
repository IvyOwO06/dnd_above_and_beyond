<?php

require_once 'dmFunctions.php';
require_once 'functions.php';

function getCharactersForCampaign($campaignId)
{
    $db = dbConnect();

    // Include c.userId in the SELECT statement
    $sql = "SELECT c.*, cc.userId as campaignCharacterUserId 
            FROM campaignCharacters cc 
            JOIN characters c ON cc.characterId = c.characterId 
            WHERE cc.campaignId = ?";

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();

    $characters = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $db->close();

    return $characters;
}

function getUsersForCampaign($campaignId)
{
    $db = dbConnect();

    $sql = "
        SELECT u.userId, u.userName 
        FROM campaignUsers cu 
        JOIN user u ON cu.userId = u.userId 
        WHERE cu.campaignId = ?
    ";

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $db->close();

    return $users;
}

function getInvitedCampaigns($userId)
{
    $db = dbConnect();

    $sql = "
        SELECT c.* 
        FROM campaignUsers cu
        JOIN campaign c ON cu.campaignId = c.campaignId
        WHERE cu.userId = ?
    ";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $campaigns = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $db->close();

    return $campaigns;
}

function getcampaign($campaignId)
{
    $db = dbConnect();

    $sql = "SELECT * FROM campaign WHERE campaignId = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();

    $campaign = $result->fetch_assoc();

    $stmt->close();
    $db->close();

    return $campaign;
}

function displaycampaigns($userId)
{
    $userId = filter_var($userId, FILTER_VALIDATE_INT);
    if ($userId === false) {
        die('Invalid user ID');
    }
    
    $campaigns = getInvitedCampaigns($userId);

    // remove campaign
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeCampaign'])) {
        $campaignId = $_POST['campaignId'];
        $campaign = getCampaign($campaignId);
        
        if ($campaign && $campaign['userId'] == $_SESSION['user']['id']) {
            removeCampaign($campaignId);
        } else {
            echo "You are not authorized to delete this campaign.";
        }
    }

    echo "<div id='campaigns'>";
    foreach($campaigns as $campaign)
    {
        ?>
        <campaign>
            <h1><?php echo htmlspecialchars($campaign['name']); ?></h1>
            <p><?php echo htmlspecialchars($campaign['description']); ?></p>
            <a href="campaign?campaignId=<?php echo htmlspecialchars($campaign['campaignId']); ?>">View</a>
            <form method="POST">
                <input type="hidden" name="campaignId" value="<?php echo htmlspecialchars($campaign['campaignId']); ?>">
                <?php
                if (isset($_SESSION['user']['id']) && $campaign && $campaign['userId'] == $_SESSION['user']['id']) {
                ?>
                <button name="removeCampaign" onclick="return confirm('Remove the campaign?')">Remove</button>
                <?php } ?>
            </form>
        </campaign>
        <?php
    }
    echo "</div>";
}

function displaycampaign($campaignId)
{
    $campaign = getCampaign($campaignId);
    $users = getUsersForCampaign($campaignId);

    if (!$campaign) {
        die("Campaign not found.");
    }
    
    $isCreator = $_SESSION['user']['id'] === (int)$campaign['userId'];
    $characters = getCharactersForCampaign($campaignId);

    // Handle user removal request (only if creator)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeUser']) && $isCreator) {
        $userIdToRemove = $_POST['removeUserId'];
        $removeCampaignId = $_POST['campaignId'];

        if ($userIdToRemove && $removeCampaignId) {
            removeUserFromCampaign($userIdToRemove, $removeCampaignId);
        }
    }

    // Handle user removal request (only if user)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeUser']) && !$isCreator) {
        $userIdToRemove = $_POST['removeUserId'];
        $removeCampaignId = $_POST['campaignId'];

        if ($userIdToRemove && $removeCampaignId) {
            removeUserFromCampaign($userIdToRemove, $removeCampaignId);
        }
    }

    // Add this to handle character removal
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeCharacter'])) {
        $characterId = $_POST['removeCharacterId'] ?? null;
        $campaignId = $_POST['campaignId'] ?? null;
        
        if ($characterId && $campaignId) {
            removeCharacterFromCampaign($characterId, $campaignId);
        }
    }

    // Add this to handle character addition
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCharacter'])) {
        $characterId = $_POST['addCharacterId'] ?? null;
        $campaignId = $_POST['campaignId'] ?? null;
        
        if ($characterId && $campaignId) {
            addCharacterToCampaign($characterId, $campaignId);
        }
    }

    ?>
    <h1><?php echo htmlspecialchars($campaign['name']); ?></h1>
    <p><?php echo htmlspecialchars($campaign['description']); ?></p>

    <?php 
    // Add DM Corner link for campaign creator
    if ($isCreator) {
        echo '<p><a href="dm_notes.php?campaignId=' . htmlspecialchars($campaignId) . '">DM Corner - Manage Notes</a></p>';
    }

    if (!$isCreator) 
    {
        ?>
        <form method="POST">
            <input type="hidden" name="removeUserId" value="<?php echo htmlspecialchars($_SESSION['user']['id']); ?>">
            <input type="hidden" name="campaignId" value="<?php echo htmlspecialchars($campaignId); ?>">
            <button name="removeUser" onclick="return confirm('Leave the campaign?')">Leave</button>
        </form>
        <?php
    }
    if ($isCreator)
    {
        addUser();
    }
    ?>

    <h2>Users</h2>
    <?php foreach ($users as $user): ?>
        <?php echo htmlspecialchars($user['userName']); ?>
        <?php if ((int)$user['userId'] === (int)$campaign['userId']): ?>
            (creator)
        <?php elseif ($isCreator): ?>
            <form method="POST">
                <input type="hidden" name="removeUserId" value="<?php echo htmlspecialchars($user['userId']); ?>">
                <input type="hidden" name="campaignId" value="<?php echo htmlspecialchars($campaignId); ?>">
                <button name="removeUser" onclick="return confirm('Remove user and all their characters?')">Remove</button>
            </form>
        <?php endif; ?>
        <br>
    <?php endforeach; ?>

    <h2>Characters</h2>
    <button onclick="openAddCharacterModal()">Add Characters</button>

    <?php if (!empty($characters)): ?>
        <ul>
        <?php foreach ($characters as $character): 
            $isCharacterOwner = ($character['userId'] == $_SESSION['user']['id']);
            $canRemove = $isCreator || $isCharacterOwner;
            ?>
            <li>
                <?php echo htmlspecialchars($character['characterName']); ?>
                <?php if ($canRemove): ?>
                    <form method="POST">
                        <input type="hidden" name="removeCharacterId" value="<?php echo $character['characterId']; ?>">
                        <input type="hidden" name="campaignId" value="<?php echo $campaignId; ?>">
                        <button type="submit" name="removeCharacter" onclick="return confirm('Remove this character from campaign?')">
                            Remove
                        </button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No characters linked to this campaign.</p>
    <?php endif; ?>
    
    <!-- Modal HTML - Fixed version -->
    <div id="addCharacterModal">
        <div id="addCharacterModalInside">
            <h2>Add Characters to Campaign</h2>
            <input type="text" id="characterSearch" placeholder="Search characters..." onkeyup="searchCharacters()">
            <div id="characterResults">
                <?php echo getAvailableCharactersList($campaignId); ?>
            </div>
            <button onclick="closeAddCharacterModal()">Close</button>
        </div>
    </div>
    
    <script>
    function openAddCharacterModal() {
        document.getElementById("addCharacterModal").style.display = "block";
        document.getElementById("characterSearch").focus();
    }
    
    function closeAddCharacterModal() {
        document.getElementById("addCharacterModal").style.display = "none";
    }
    
    function searchCharacters() {
        const searchTerm = document.getElementById("characterSearch").value.toLowerCase();
        const characters = document.querySelectorAll(".character-item");
        
        characters.forEach(character => {
            const name = character.getAttribute("data-name").toLowerCase();
            if (name.includes(searchTerm)) {
                character.style.display = "block";
            } else {
                character.style.display = "none";
            }
        });
    }
    </script>
    <?php
}

function createcampaign($userId)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaignName'])) {
        $db = dbConnect();

        // Sanitize inputs
        $name = $db->real_escape_string(trim($_POST['campaignName']));
        $description = $db->real_escape_string(trim($_POST['description'] ?? ''));

        // Validate
        if (!$userId || empty($name)) {
            echo "Missing user or campaign name.";
            return;
        }

        // Insert into campaign table
        $sql = "INSERT INTO campaign (userId, name, description) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iss", $userId, $name, $description);

        if ($stmt->execute()) {
            $campaignId = $stmt->insert_id; // Get the ID of the newly created campaign

            // Now add the creator to campaignUsers
            $linkSql = "INSERT INTO campaignUsers (userId, campaignId) VALUES (?, ?)";
            $linkStmt = $db->prepare($linkSql);
            $linkStmt->bind_param("ii", $userId, $campaignId);
            $linkStmt->execute();
            $linkStmt->close();

            echo "Campaign created and user added.";
        } else {
            echo "Error creating campaign: " . $stmt->error;
        }
        
        header("Location: campaigns?userId=$userId");

        $stmt->close();
        $db->close();
    }
}

function addUser()
{
    ?>
    <form method="GET">
        <input type="hidden" name="campaignId" value="<?php echo htmlspecialchars($_GET['campaignId'] ?? ''); ?>" />
        <input type="search" name="search" placeholder="Add users..." />
    </form>
    <?php

    $userName = $_GET['search'] ?? '';
    $campaignId = $_GET['campaignId'] ?? '';

    if ($userName && $campaignId) {
        $matches = searchProfileByName($userName);

        foreach ($matches as $match) {
            echo htmlspecialchars($match['userName']) . "<br>";

            if (!isUserInCampaign($match['userId'], $campaignId)) {
                ?>
                <form method="POST">
                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars($match['userId']); ?>">
                    <input type="hidden" name="campaignId" value="<?php echo htmlspecialchars($campaignId); ?>">
                    <button name="addUser">Add</button>
                </form>
                <?php
            } else {
                echo "<em>Already in campaign</em><br>";
            }

            echo "<br>";
        }
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
        $userId = $_POST['userId'] ?? null;
        $campaignId = $_POST['campaignId'] ?? null;

        if ($userId && $campaignId) {
            addUserToCampaign($userId, $campaignId);
        }
    }
}

function isUserInCampaign($userId, $campaignId)
{
    $db = dbConnect();

    $sql = "SELECT 1 FROM campaignUsers WHERE userId = ? AND campaignId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $userId, $campaignId);
    $stmt->execute();
    $stmt->store_result();

    $inCampaign = $stmt->num_rows > 0;

    $stmt->close();
    $db->close();

    return $inCampaign;
}

function addUserToCampaign($userId,$campaignId)
{
    $db = dbConnect();

    // Check if the user is already added to avoid duplicates
    $checkSql = "SELECT * FROM campaignUsers WHERE userId = ? AND campaignId = ?";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->bind_param("ii", $userId, $campaignId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo "User is already in this campaign.";
        return;
    }

    // If not already added, insert the user
    $sql = "INSERT INTO campaignUsers (userId, campaignId) VALUES (?, ?)";
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("ii", $userId, $campaignId);

    if ($stmt->execute()) {
        echo "User successfully added to campaign.";
    } else {
        echo "Error: " . $stmt->error;
    }

    header("Location: campaign?campaignId=$campaignId");

    $stmt->close();
    $db->close();
}

function removeUserFromCampaign($userId, $campaignId)
{
    $db = dbConnect();

    // Step 1: Delete all characters the user added to this campaign
    $deleteCharactersSql = "DELETE FROM campaignCharacters WHERE userId = ? AND campaignId = ?";
    $stmt1 = $db->prepare($deleteCharactersSql);
    $stmt1->bind_param("ii", $userId, $campaignId);
    $stmt1->execute();
    $stmt1->close();

    // Step 2: Remove the user from the campaign
    $deleteUserSql = "DELETE FROM campaignUsers WHERE userId = ? AND campaignId = ?";
    $stmt2 = $db->prepare($deleteUserSql);
    $stmt2->bind_param("ii", $userId, $campaignId);
    $stmt2->execute();
    $stmt2->close();

    $db->close();

    echo "User and their characters removed from campaign.";
    header("Location: campaign?campaignId=$campaignId");
    exit;
}

function removeCampaign($campaignId)
{
    $db = dbConnect();

    // Step 1: Delete all characters linked to this campaign
    $deleteCharactersSql = "DELETE FROM campaignCharacters WHERE campaignId = ?";
    $stmt1 = $db->prepare($deleteCharactersSql);
    $stmt1->bind_param("i", $campaignId);
    $stmt1->execute();
    $stmt1->close();

    // Step 2: Delete all user links from campaignUsers
    $deleteUsersSql = "DELETE FROM campaignUsers WHERE campaignId = ?";
    $stmt2 = $db->prepare($deleteUsersSql);
    $stmt2->bind_param("i", $campaignId);
    $stmt2->execute();
    $stmt2->close();

    // Step 3: Finally, delete the campaign itself
    $deleteCampaignSql = "DELETE FROM campaign WHERE campaignId = ?";
    $stmt3 = $db->prepare($deleteCampaignSql);
    $stmt3->bind_param("i", $campaignId);
    $stmt3->execute();
    $stmt3->close();

    $db->close();

    echo "Campaign and all related data removed.";
    header("Location: campaigns?userId=" . $_SESSION['user']['id']);
    exit;
}


function getAvailableCharactersList($campaignId) {
    $db = dbConnect();
    
    // Get characters not already in this campaign that belong to the current user
    $sql = "SELECT c.* FROM characters c 
            LEFT JOIN campaignCharacters cc ON c.characterId = cc.characterId AND cc.campaignId = ?
            WHERE cc.characterId IS NULL AND c.userId = ?";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $campaignId, $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $html = '';
    while ($character = $result->fetch_assoc()) {
        $html .= '
        <div class="characterItem" data-name="'.htmlspecialchars($character['characterName']).'">
            '.htmlspecialchars($character['characterName']).'
            <form method="POST" class="addCharacterForm">
                <input type="hidden" name="addCharacterId" value="'.$character['characterId'].'">
                <input type="hidden" name="campaignId" value="'.$campaignId.'">
                <button type="submit" name="addCharacter">Add</button>
            </form>
        </div>
        ';
    }
    
    if (empty($html)) {
        $html = '<p>No characters available to add.</p>';
    }
    
    $stmt->close();
    $db->close();
    
    return $html;
}

function addCharacterToCampaign($characterId, $campaignId) {
    $db = dbConnect();
    
    $sql = "INSERT INTO campaignCharacters (characterId, campaignId, userId) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $characterId, $campaignId, $_SESSION['user']['id']);
    
    if ($stmt->execute()) {
        header("Location: campaign?campaignId=$campaignId");
        exit;
    } else {
        echo "Error adding character: ".$stmt->error;
    }
    
    $stmt->close();
    $db->close();
}

function removeCharacterFromCampaign($characterId, $campaignId) {
    $db = dbConnect();
    
    // First get the character and campaign info to verify permissions
    $checkSql = "SELECT c.userId as characterOwnerId, 
                        cc.userId as addedByUserId,
                        camp.userId as campaignOwnerId
                 FROM campaignCharacters cc
                 JOIN characters c ON cc.characterId = c.characterId
                 JOIN campaign camp ON cc.campaignId = camp.campaignId
                 WHERE cc.characterId = ? AND cc.campaignId = ?";
    
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->bind_param("ii", $characterId, $campaignId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $data = $result->fetch_assoc();
    $checkStmt->close();
    
    if (!$data) {
        echo "Character not found in this campaign.";
        return;
    }
    
    $currentUserId = $_SESSION['user']['id'];
    $isCreator = ($data['campaignOwnerId'] == $currentUserId);
    $isCharacterOwner = ($data['characterOwnerId'] == $currentUserId);
    $isCharacterAdder = ($data['addedByUserId'] == $currentUserId);
    
    // Only allow removal if:
    // 1. User is campaign creator, OR
    // 2. User owns the character, OR
    // 3. User was the one who added the character to campaign
    if ($isCreator || $isCharacterOwner || $isCharacterAdder) {
        $deleteSql = "DELETE FROM campaignCharacters WHERE characterId = ? AND campaignId = ?";
        $deleteStmt = $db->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $characterId, $campaignId);
        
        if ($deleteStmt->execute()) {
            header("Location: campaign?campaignId=$campaignId");
            exit;
        } else {
            echo "Error removing character: ".$deleteStmt->error;
        }
        
        $deleteStmt->close();
    } else {
        echo "You don't have permission to remove this character.";
    }
    
    $db->close();
}
?>