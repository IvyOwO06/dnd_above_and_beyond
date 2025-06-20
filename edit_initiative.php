<?php
session_start();
require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

// Get initiativeId from URL
$initiativeId = $_GET['initiativeId'] ?? null;
if (!$initiativeId) {
    die("No initiative specified.");
}

// Fetch the initiative
$initiative = getInitiative($initiativeId);
if (!$initiative) {
    die("Initiative not found.");
}

// Fetch session and campaign details to verify creator
$session = getSession($initiative['sessionId']);
if (!$session) {
    die("Session not found.");
}
$campaign = getcampaign($session['campaignId']);
if (!$campaign) {
    die("Campaign not found.");
}

// Check if the current user is the campaign creator
if ($_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not authorized to edit this initiative.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $initiativeName = $_POST['initiativeName'] ?? '';
    $initiativeValue = $_POST['initiative'] ?? '';
    $isNPC = isset($_POST['isNPC']) ? 1 : 0;
    if ($initiativeName && is_numeric($initiativeValue)) {
        updateInitiative($initiativeId, $initiativeName, $initiativeValue, $isNPC);
        header("Location: dm_initiative.php?sessionId=" . $initiative['sessionId']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Initiative</title>
</head>
<body>
    <h1>Edit Initiative for <?php echo htmlspecialchars($session['sessionName']); ?></h1>
    <form method="POST">
        <input type="text" name="initiativeName" value="<?php echo htmlspecialchars($initiative['initiativeName']); ?>" required>
        <input type="number" name="initiative" value="<?php echo htmlspecialchars($initiative['initiative']); ?>" required>
        <label><input type="checkbox" name="isNPC" <?php echo $initiative['isNPC'] ? 'checked' : ''; ?>> Is NPC?</label>
        <button type="submit">Update Initiative</button>
    </form>
    <a href="dm_initiative.php?sessionId=<?php echo $initiative['sessionId']; ?>">Cancel</a>
</body>
</html> 