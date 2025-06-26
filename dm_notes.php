<?php
require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';
require_once 'inc/navFunctions.php';

$campaignId = $_GET['campaignId'] ?? null;
if (!$campaignId) {
    die("No campaign specified.");
}

$campaign = getCampaign($campaignId);
if (!$campaign) {
    die("Campaign not found.");
}

if ($_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not the creator of this campaign.");
}

$notes = getNotesForCampaign($campaignId);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1800">
    <title>DM Corner - Notes</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/notes.css">
    <?php displayHeader(); ?>
</head>
<body>
<h1>Notes for <?php echo htmlspecialchars($campaign['name']); ?></h1>

<div class="note-container">

    <form method="POST" class="note-form">
        <input type="text" name="noteTitle" placeholder="Note Title" required>
        <textarea name="noteContent" placeholder="Note Content" required></textarea>
        <button type="submit" name="createNote">Create Note</button>
    </form>

    <?php if (!empty($notes)): ?>
        <?php foreach ($notes as $note): ?>
            <div class="note-card">
                <h3><?php echo htmlspecialchars($note['noteTitle']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($note['noteContent'])); ?></p>
                <p><small>Created: <?php echo $note['noteCreatedAt']; ?></small></p>
                <div class="note-card-actions">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="noteId" value="<?php echo $note['noteId']; ?>">
                        <button type="submit" name="deleteNote" onclick="return confirm('Delete this note?')">Delete</button>
                    </form>
                    <a href="edit_note.php?noteId=<?php echo $note['noteId']; ?>">Edit</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No notes yet. Create one above!</p>
    <?php endif; ?>

    <div class="note-navigation">
        <a href="campaign?campaignId=<?php echo $campaignId; ?>">Back to Campaign</a>
        <a href="dm_sessions.php?campaignId=<?php echo $campaignId; ?>">Manage Sessions</a>
        <a href="dm_quests.php?campaignId=<?php echo $campaignId; ?>">Manage Quests</a>
        <a href="dm_npcs.php?campaignId=<?php echo $campaignId; ?>">Manage NPCs</a>
    </div>

</div>
    <?php
    displayFooter();
    ?>
