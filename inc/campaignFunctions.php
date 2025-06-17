<?php
function getCharactersForCampaign($campaignId)
{
    $db = dbConnect();

    $sql = "
        SELECT c.*
        FROM campaignCharacters cc
        JOIN characters c ON cc.characterId = c.characterId
        WHERE cc.campaignId = ?
    ";

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

function getcampaigns($userId)
{
    $db = dbConnect();

    $sql = "SELECT * FROM campaign WHERE userId = ?";
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
    
    $campaigns = getcampaigns($userId);

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
}

function displaycampaign($campaignId)
{
    $campaign = getCampaign($campaignId);
    if (!$campaign) {
        die("Campaign not found.");
    }

    $characters = getCharactersForCampaign($campaignId); // Get related characters

    ?>
    <h1><?php echo htmlspecialchars($campaign['name']); ?></h1>
    <p><?php echo htmlspecialchars($campaign['description']); ?></p>

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
        $userId;

        // Validate
        if (!$userId || empty($name)) {
            echo "Missing user or campaign name.";
            return;
        }

        // Insert into campaign table
        $sql = "INSERT INTO campaign (userId, name, description) VALUES ($userId, '$name', '$description')";

        if ($db->query($sql)) {
            echo "Campaign created successfully.";
        } else {
            echo "Error creating campaign: " . $db->error;
        }
    }
}

?>