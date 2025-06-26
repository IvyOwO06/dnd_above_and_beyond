<?php

require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';
require_once 'inc/navFunctions.php';    

// Get campaignId from URL
$campaignId = $_GET['campaignId'] ?? null;
if (!$campaignId) {
    die("No campaign specified.");
}

// Fetch campaign details
$campaign = getcampaign($campaignId);
if (!$campaign) {
    die("Campaign not found.");
}

// Check if the current user is the campaign creator
if (!isset($_SESSION['user']['id']) || $_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not the creator of this campaign.");
}

// Get all quests for this campaign
$quests = getQuestsForCampaign($campaignId);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createQuest'])) {
        $questTitle = $_POST['questTitle'] ?? '';
        $questDescription = $_POST['questDescription'] ?? '';
        $questStatus = $_POST['questStatus'] ?? 'Not Started';
        if ($questTitle && $questDescription && in_array($questStatus, ['Not Started', 'In Progress', 'Completed'])) {
            createQuest($campaignId, $questTitle, $questDescription, $questStatus);
            header("Location: dm_quests.php?campaignId=$campaignId");
            exit;
        } else {
            echo "Invalid quest details.";
        }
    } elseif (isset($_POST['deleteQuest'])) {
        $questId = $_POST['questId'] ?? null;
        if ($questId) {
            deleteQuest($questId);
            header("Location: dm_quests.php?campaignId=$campaignId");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DM Corner - Quests</title>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1800">
    <title>DM Corner - Notes</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/quests.css">
    <link rel="stylesheet" href="css/notes.css">

    <?php displayHeader(); ?>
</head>
<body><h1>Quests for <?php echo htmlspecialchars($campaign['name']); ?></h1>

<div class="quest-container">

    <form method="POST" class="quest-form">
        <input type="text" name="questTitle" placeholder="Quest Title" required>
        <textarea name="questDescription" placeholder="Quest Description" required></textarea>
        <select name="questStatus" required>
            <option value="Not Started">Not Started</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select>
        <button type="submit" name="createQuest">Create Quest</button>
    </form>

    <?php if (!empty($quests)): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($quests as $quest): ?>
                <tr>
                    <td><?php echo htmlspecialchars($quest['questTitle']); ?></td>
                    <td><?php echo htmlspecialchars($quest['questDescription']); ?></td>
                    <td><?php echo htmlspecialchars($quest['questStatus']); ?></td>
                    <td>
                        <a href="edit_quest.php?questId=<?php echo $quest['questId']; ?>">Edit</a>
                        <form method="POST">
                            <input type="hidden" name="questId" value="<?php echo $quest['questId']; ?>">
                            <button type="submit" name="deleteQuest" onclick="return confirm('Delete this quest?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No quests yet. Create one above!</p>
    <?php endif; ?>

    <a href="dm_notes.php?campaignId=<?php echo $campaignId; ?>" class="back-link">Back to DM Notes</a>
    <a href="dm_sessions.php?campaignId=<?php echo $campaignId; ?>" class="back-link">Manage Sessions</a>
</div>
