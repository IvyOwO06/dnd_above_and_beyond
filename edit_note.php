<?php

require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

// Get noteId from URL
$noteId = $_GET['noteId'] ?? null;
if (!$noteId) {
    die("No note specified.");
}

// Fetch the note
$note = getNote($noteId);
if (!$note) {
    die("Note not found.");
}

// Verify that the current user is the creator of the campaign
$campaign = getCampaign($note['campaignId']);
if ($_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not authorized to edit this note.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['noteTitle'] ?? '';
    $content = $_POST['noteContent'] ?? '';
    if ($title && $content) {
        updateNote($noteId, $title, $content);
        header("Location: dm_notes.php?campaignId=" . $note['campaignId']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Note</title>
</head>
<body>
    <h1>Edit Note</h1>
    <form method="POST">
        <input type="text" name="noteTitle" value="<?php echo htmlspecialchars($note['noteTitle']); ?>" required>
        <textarea name="noteContent" required><?php echo htmlspecialchars($note['noteContent']); ?></textarea>
        <button type="submit">Update Note</button>
    </form>
    <a href="dm_notes.php?campaignId=<?php echo $note['campaignId']; ?>">Cancel</a>
</body>
</html>