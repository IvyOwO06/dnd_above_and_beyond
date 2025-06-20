<?php
require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

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
    <title>DM Corner - Initiative Tracker</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Initiative Tracker for <?php echo htmlspecialchars($session['sessionName']); ?></h1>

    <!-- Form to add a new initiative -->
    <form method="POST">
        <input type="text" name="initiativeName" placeholder="Character/NPC Name" required>
        <input type="number" name="initiative" placeholder="Initiative Value" required>
        <label><input type="checkbox" name="isNPC"> Is NPC?</label>
        <button type="submit" name="createInitiative">Add Initiative</button>
    </form>

    <!-- Display initiative table -->
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
                    <td>
                        <a href="edit_initiative.php?initiativeId=<?php echo $initiative['initiativeId']; ?>">Edit</a>
                        <form method="POST" style="display:inline;">
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

    <a href="dm_sessions.php?campaignId=<?php echo $session['campaignId']; ?>">Back to Sessions</a>
</body>
</html>
