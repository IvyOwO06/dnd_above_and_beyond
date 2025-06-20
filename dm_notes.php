<?php
require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

// Get campaignId from URL
$campaignId = $_GET['campaignId'] ?? null;
if (!$campaignId) {
    die("No campaign specified.");
}

// Fetch campaign details
$campaign = getCampaign($campaignId);
if (!$campaign) {
    die("Campaign not found.");
}

// Check if the current user is the campaign creator
if ($_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not the creator of this campaign.");
}

// Get all notes for this campaign
$notes = getNotesForCampaign($campaignId);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createNote'])) {
        $title = $_POST['noteTitle'] ?? '';
        $content = $_POST['noteContent'] ?? '';
        if ($title && $content) {
            createNote($campaignId, $title, $content);
            header("Location: dm_notes.php?campaignId=$campaignId");
            exit;
        }
    } elseif (isset($_POST['deleteNote'])) {
        $noteId = $_POST['noteId'] ?? null;
        if ($noteId) {
            deleteNote($noteId);
            header("Location: dm_notes.php?campaignId=$campaignId");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DM Corner - Notes</title>
</head>
<body>
    <h1>Notes for <?php echo htmlspecialchars($campaign['name']); ?></h1>

    <!-- Form to create a new note -->
    <form method="POST">
        <input type="text" name="noteTitle" placeholder="Note Title" required>
        <textarea name="noteContent" placeholder="Note Content" required></textarea>
        <button type="submit" name="createNote">Create Note</button>
    </form>

    <!-- Display existing notes -->
    <?php if (!empty($notes)): ?>
        <?php foreach ($notes as $note): ?>
            <div>
                <h3><?php echo htmlspecialchars($note['noteTitle']); ?></h3>
                <p><?php echo htmlspecialchars($note['noteContent']); ?></p>
                <p><small>Created: <?php echo $note['noteCreatedAt']; ?></small></p>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="noteId" value="<?php echo $note['noteId']; ?>">
                    <button type="submit" name="deleteNote" onclick="return confirm('Delete this note?')">Delete</button>
                </form>
                <a href="edit_note.php?noteId=<?php echo $note['noteId']; ?>">Edit</a>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No notes yet. Create one above!</p>
    <?php endif; ?>

    <a href="campaign?campaignId=<?php echo $campaignId; ?>">Back to Campaign</a>
    <a href="dm_sessions.php?campaignId=<?php echo $campaignId; ?>">Manage Sessions</a>
    <a href="dm_quests.php?campaignId=<?php echo $campaignId; ?>">Manage Quests</a><br>
    <a href="dm_npcs.php?campaignId=<?php echo $campaignId; ?>">Manage NPCs</a><br>

</body>
</html>