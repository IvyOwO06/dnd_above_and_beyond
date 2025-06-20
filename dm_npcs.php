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
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        textarea { width: 100%; height: 100px; }
        img { max-width: 100px; height: auto; }
    </style>
</head>
<body>
    <h1>NPCs for <?php echo htmlspecialchars($campaign['name']); ?></h1>

    <!-- Form to create a new NPC -->
    <form method="POST" enctype="multipart/form-data">
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
        <table>
            <tr>
                <th>Name</th>
                <th>Race</th>
                <th>Class</th>
                <th>Image</th>
                <th>Description</th>
                <th>Friendly</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($npcs as $npc): ?>
                <tr>
                    <td><?php echo htmlspecialchars($npc['npcName']); ?></td>
                    <td><?php echo htmlspecialchars($npc['npcRace']); ?></td>
                    <td><?php echo htmlspecialchars($npc['npcClass']); ?></td>
                    <td>
                        <?php if ($npc['npcImage']): ?>
                            <img src="<?php echo htmlspecialchars($npc['npcImage']); ?>" alt="NPC Image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($npc['npcDescription']); ?></td>
                    <td><?php echo $npc['npcIsFriendly'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="edit_npc.php?npcId=<?php echo $npc['npcId']; ?>">Edit</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="npcId" value="<?php echo $npc['npcId']; ?>">
                            <button type="submit" name="deleteNPC" onclick="return confirm('Delete this NPC?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No NPCs yet. Create one above!</p>
    <?php endif; ?>

    <a href="dm_notes.php?campaignId=<?php echo $campaignId; ?>">Back to DM Notes</a><br>
    <a href="dm_sessions.php?campaignId=<?php echo $campaignId; ?>">Manage Sessions</a><br>
    <a href="dm_quests.php?campaignId=<?php echo $campaignId; ?>">Manage Quests</a>
</body>
</html>