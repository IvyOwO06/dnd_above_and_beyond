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

// Get all NPCs for this campaign
$npcs = getNPCsForCampaign($campaignId);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createNPC'])) {
        $npcName = $_POST['npcName'] ?? '';
        $npcRace = $_POST['npcRace'] ?? '';
        $npcClass = $_POST['npcClass'] ?? '';
        $npcDescription = $_POST['npcDescription'] ?? '';
        $npcIsFriendly = isset($_POST['npcIsFriendly']) ? 1 : 0;
        $npcImage = '';

        // Handle image upload
        if (isset($_FILES['npcImage']) && $_FILES['npcImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $fileName = uniqid() . '_' . basename($_FILES['npcImage']['name']);
            $uploadFile = $uploadDir . $fileName;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            if (in_array($_FILES['npcImage']['type'], $allowedTypes) && $_FILES['npcImage']['size'] <= $maxFileSize) {
                if (move_uploaded_file($_FILES['npcImage']['tmp_name'], $uploadFile)) {
                    $npcImage = $uploadFile;
                } else {
                    echo "Failed to move uploaded file.";
                }
            } else {
                echo "Invalid file type or size. Only JPEG, PNG, GIF up to 2MB allowed.";
            }
        }

        if ($npcName && $npcRace && $npcClass && $npcDescription) {
            createNPC($campaignId, $npcName, $npcRace, $npcClass, $npcImage, $npcDescription, $npcIsFriendly);
            header("Location: dm_npcs.php?campaignId=$campaignId");
            exit;
        } else {
            echo "Please fill in all required fields.";
        }
    } elseif (isset($_POST['deleteNPC'])) {
        $npcId = $_POST['npcId'] ?? null;
        if ($npcId) {
            // Optionally delete the image file from uploads directory
            $npc = getNPC($npcId);
            if ($npc['npcImage'] && file_exists($npc['npcImage'])) {
                unlink($npc['npcImage']);
            }
            deleteNPC($npcId);
            header("Location: dm_npcs.php?campaignId=$campaignId");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DM Corner - NPCs</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1800">
    <title>DM Corner - Notes</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/npcs.css">
    <link rel="stylesheet" href="css/notes.css">
    <?php displayHeader(); ?>
</head>
<body>
        <h1>NPCs for <?php echo htmlspecialchars($campaign['name']); ?></h1>
    <div class="npc-container">
    <!-- Form to create a new NPC -->
    <form method="POST" enctype="multipart/form-data" class="npcform">
        <input type="text" name="npcName" placeholder="NPC Name" required>
        <input type="text" name="npcRace" placeholder="NPC Race" required>
        <input type="text" name="npcClass" placeholder="NPC Class" required>
        <input type="file" name="npcImage" accept="image/jpeg,image/png,image/gif">
        <textarea name="npcDescription" placeholder="NPC Description" required></textarea>
        <label><input type="checkbox" name="npcIsFriendly"> Is Friendly?</label>
        <button type="submit" name="createNPC">Create NPC</button>
    </form>

    <!-- Display existing NPCs -->
    <?php if (!empty($npcs)): ?>
  <div class="npc-grid">
    <?php foreach ($npcs as $npc): ?>
      <div class="npc-card">
        <?php if ($npc['npcImage']): ?>
          <img src="<?php echo htmlspecialchars($npc['npcImage']); ?>" alt="NPC Image">
        <?php endif; ?>
        <div class="npc-details">
          <h3><?php echo htmlspecialchars($npc['npcName']); ?></h3>
          <p><strong>Race:</strong> <?php echo htmlspecialchars($npc['npcRace']); ?></p>
          <p><strong>Class:</strong> <?php echo htmlspecialchars($npc['npcClass']); ?></p>
          <p><strong>Description:</strong> <?php echo htmlspecialchars($npc['npcDescription']); ?></p>
          <p><strong>Friendly:</strong> <?php echo $npc['npcIsFriendly'] ? 'Yes' : 'No'; ?></p>
          <div class="npc-actions">
            <a href="edit_npc.php?npcId=<?php echo $npc['npcId']; ?>">Edit</a>
            <form method="POST">
              <input type="hidden" name="npcId" value="<?php echo $npc['npcId']; ?>">
              <button type="submit" name="deleteNPC" onclick="return confirm('Delete this NPC?')">Delete</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p>No NPCs yet. Create one above!</p>
<?php endif; ?>

</div>
<div class="backlink-row">
  <a href="dm_notes.php?campaignId=<?php echo $campaignId; ?>">Back to DM Notes</a>
  <a href="dm_sessions.php?campaignId=<?php echo $campaignId; ?>">Manage Sessions</a>
  <a href="dm_quests.php?campaignId=<?php echo $campaignId; ?>">Manage Quests</a>
</div>
    <?php
    displayFooter();
    ?>
</body>
</html>