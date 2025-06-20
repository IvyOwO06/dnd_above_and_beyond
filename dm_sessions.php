<?php
require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

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
if ($_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not the creator of this campaign.");
}

// Get all sessions for this campaign
$sessions = getSessionsForCampaign($campaignId);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createSession'])) {
        $sessionName = $_POST['sessionName'] ?? '';
        $sessionDate = $_POST['sessionDate'] ?? '';
        if ($sessionName && $sessionDate) {
            createSession($campaignId, $sessionName, $sessionDate);
            header("Location: dm_sessions.php?campaignId=$campaignId");
            exit;
        }
    } elseif (isset($_POST['deleteSession'])) {
        $sessionId = $_POST['sessionId'] ?? null;
        if ($sessionId) {
            deleteSession($sessionId);
            header("Location: dm_sessions.php?campaignId=$campaignId");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DM Corner - Sessions</title>
</head>
<body>
    <h1>Sessions for <?php echo htmlspecialchars($campaign['name']); ?></h1>

    <!-- Form to create a new session -->
    <form method="POST">
        <input type="text" name="sessionName" placeholder="Session Name" required>
        <input type="date" name="sessionDate" required>
        <button type="submit" name="createSession">Create Session</button>
    </form>

    <!-- Display existing sessions -->
    <?php if (!empty($sessions)): ?>
        <?php foreach ($sessions as $session): ?>
            <div>
                <h3><?php echo htmlspecialchars($session['sessionName']); ?></h3>
                <p>Date: <?php echo htmlspecialchars($session['sessionDate']); ?></p>
                <a href="dm_initiative.php?sessionId=<?php echo $session['sessionId']; ?>">Manage Initiative</a>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="sessionId" value="<?php echo $session['sessionId']; ?>">
                    <button type="submit" name="deleteSession" onclick="return confirm('Delete this session and all its initiative entries?')">Delete</button>
                </form>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No sessions yet. Create one above!</p>
    <?php endif; ?>

    <a href="dm_notes.php?campaignId=<?php echo $campaignId; ?>">Back to DM Notes</a>
</body>
</html>