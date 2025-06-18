<?php
function getCharactersForCampaign($campaignId)
{
    $db = dbConnect();

    // c. is alias for characters and cc. is a alias for campaignCharacters
    $sql = "SELECT c.* FROM campaignCharacters cc JOIN characters c ON cc.characterId = c.characterId WHERE cc.campaignId = ?";

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

    if($_SESSION['user']['id'] == $userId)
    {
        ?>
        <form method="POST" action="campaigns?userId=<?php echo $userId ?>">
            <label for="campaignName">Create Campaign</label><br>

            <input type="text" id="campaignName" name="campaignName" placeholder="Campaign Name" required><br><br>

            <textarea id="description" name="description" placeholder="Description..." rows="2" cols="15"></textarea><br><br>

            <button type="submit">Create</button>
        </form>
        <?php
        createcampaign($userId);
    }

    echo "<div id='campaigns'>";
    foreach($campaigns as $campaign)
    {
        ?>
        <campaign>
            <h1><?php echo htmlspecialchars($campaign['name']); ?></h1>
            <p><?php echo htmlspecialchars($campaign['description']); ?></p>
            <a href="campaign?campaignId=<?php echo htmlspecialchars($campaign['campaignId']); ?>">View</a>
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

    // ✅ Handle user removal request (only if creator)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeUser']) && $isCreator) {
        $userIdToRemove = $_POST['removeUserId'] ?? null;
        $removeCampaignId = $_POST['campaignId'] ?? null;

        if ($userIdToRemove && $removeCampaignId) {
            removeUserFromCampaign($userIdToRemove, $removeCampaignId);
        }
    }

    ?>
    <h1><?php echo htmlspecialchars($campaign['name']); ?></h1>
    <p><?php echo htmlspecialchars($campaign['description']); ?></p>

    <?php if ($isCreator): ?>
        <?php addUser(); ?>
    <?php endif; ?>

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
    <?php if (!empty($characters)): ?>
        <ul>
        <?php foreach ($characters as $character): ?>
            <li><?php echo htmlspecialchars($character['characterName']); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No characters linked to this campaign.</p>
    <?php endif;
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

?>