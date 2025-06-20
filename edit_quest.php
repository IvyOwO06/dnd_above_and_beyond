<?php

require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

// Get questId from URL
$questId = $_GET['questId'] ?? null;
if (!$questId) {
    die("No quest specified.");
}

// Fetch the quest
$quest = getQuest($questId);
if (!$quest) {
    die("Quest not found.");
}

// Fetch campaign details to verify creator
$campaign = getcampaign($quest['campaignId']);
if (!$campaign) {
    die("Campaign not found.");
}

// Check if the current user is the campaign creator
if (!isset($_SESSION['user']['id']) || $_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not authorized to edit this quest.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questTitle = $_POST['questTitle'] ?? '';
    $questDescription = $_POST['questDescription'] ?? '';
    $questStatus = $_POST['questStatus'] ?? 'Not Started';
    if ($questTitle && $questDescription && in_array($questStatus, ['Not Started', 'In Progress', 'Completed'])) {
        updateQuest($questId, $questTitle, $questDescription, $questStatus);
        header("Location: dm_quests.php?campaignId=" . $quest['campaignId']);
        exit;
    } else {
        echo "Invalid quest details.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quest</title>
    <style>
        textarea { width: 100%; height: 100px; }
    </style>
</head>
<body>
    <h1>Edit Quest</h1>
    <form method="POST">
        <input type="text" name="questTitle" value="<?php echo htmlspecialchars($quest['questTitle']); ?>" required>
        <textarea name="questDescription" required><?php echo htmlspecialchars($quest['questDescription']); ?></textarea>
        <select name="questStatus" required>
            <option value="Not Started" <?php echo $quest['questStatus'] === 'Not Started' ? 'selected' : ''; ?>>Not Started</option>
            <option value="In Progress" <?php echo $quest['questStatus'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
            <option value="Completed" <?php echo $quest['questStatus'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
        </select>
        <button type="submit">Update Quest</button>
    </form>
    <a href="dm_quests.php?campaignId=<?php echo $quest['campaignId']; ?>">Cancel</a>
</body>
</html>