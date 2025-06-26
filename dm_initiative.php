<?php
require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';
require_once 'inc/navFunctions.php';

// Get sessionId from URL
$sessionId = $_GET['sessionId'] ?? null;
if (!$sessionId) {
    die("No session specified.");
}

// Fetch session details
$session = getSession($sessionId);
if (!$session) {
    die("Session not found.");
}

// Fetch campaign details to verify creator
$campaign = getcampaign($session['campaignId']);
if (!$campaign) {
    die("Campaign not found.");
}

// Check if the current user is the campaign creator
if ($_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not the creator of this campaign.");
}

// Get all initiatives for this session
$initiatives = getInitiativesForSession($sessionId);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createInitiative'])) {
        $initiativeName = $_POST['initiativeName'] ?? '';
        $initiative = $_POST['initiative'] ?? '';
        $isNPC = isset($_POST['isNPC']) ? 1 : 0;
        if ($initiativeName && is_numeric($initiative)) {
            createInitiative($sessionId, $initiativeName, $initiative, $isNPC);
            header("Location: dm_initiative.php?sessionId=$sessionId");
            exit;
        }
    } elseif (isset($_POST['deleteInitiative'])) {
        $initiativeId = $_POST['initiativeId'] ?? null;
        if ($initiativeId) {
            deleteInitiative($initiativeId);
            header("Location: dm_initiative.php?sessionId=$sessionId");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1800">
    <title>DM Corner - Notes</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/initiative.css">
        <link rel="stylesheet" href="css/notes.css">

    <?php displayHeader(); ?>
</head>
    <title>DM Corner - Initiative Tracker</title>
</head>
<body><h1>Initiative Tracker for <?php echo htmlspecialchars($session['sessionName']); ?></h1>

<div class="initiative-container">

    <form method="POST" class="initiative-form">
        <input type="text" name="initiativeName" placeholder="Character/NPC Name" required>
        <input type="number" name="initiative" placeholder="Initiative Value" required>
        <label><input type="checkbox" name="isNPC"> Is NPC?</label>
        <button type="submit" name="createInitiative">Add Initiative</button>
    </form>

    <?php if (!empty($initiatives)): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Initiative</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($initiatives as $initiative): ?>
                <tr>
                    <td><?php echo htmlspecialchars($initiative['initiativeName']); ?></td>
                    <td><?php echo htmlspecialchars($initiative['initiative']); ?></td>
                    <td><?php echo $initiative['isNPC'] ? 'NPC' : 'Character'; ?></td>
                    <td class="actions">
  <a href="edit_initiative.php?initiativeId=<?php echo $initiative['initiativeId']; ?>">Edit</a>
  <form method="POST" style="margin:0;">
    <input type="hidden" name="initiativeId" value="<?php echo $initiative['initiativeId']; ?>">
    <button type="submit" name="deleteInitiative" onclick="return confirm('Delete this initiative entry?')">Delete</button>
  </form>
</td>

                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No initiative entries yet. Add one above!</p>
    <?php endif; ?>

    <a href="dm_sessions.php?campaignId=<?php echo $session['campaignId']; ?>" class="back-to-sessions">Back to Sessions</a>

</div>
    <?php
    displayFooter();
    ?>
