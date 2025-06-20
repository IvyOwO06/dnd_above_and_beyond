<?php

require_once 'inc/campaignFunctions.php';
require_once 'inc/dmFunctions.php';

// Get npcId from URL
$npcId = $_GET['npcId'] ?? null;
if (!$npcId) {
    die("No NPC specified.");
}

// Fetch the NPC
$npc = getNPC($npcId);
if (!$npc) {
    die("NPC not found.");
}

// Fetch campaign details to verify creator
$campaign = getcampaign($npc['campaignId']);
if (!$campaign) {
    die("Campaign not found.");
}

// Check if the current user is the campaign creator
if (!isset($_SESSION['user']['id']) || $_SESSION['user']['id'] !== (int)$campaign['userId']) {
    die("You are not authorized to edit this NPC.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npcName = $_POST['npcName'] ?? '';
    $npcRace = $_POST['npcRace'] ?? '';
    $npcClass = $_POST['npcClass'] ?? '';
    $npcDescription = $_POST['npcDescription'] ?? '';
    $npcIsFriendly = isset($_POST['npcIsFriendly']) ? 1 : 0;
    $npcImage = $npc['npcImage']; // Keep existing image by default

    // Handle image upload
    if (isset($_FILES['npcImage']) && $_FILES['npcImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['npcImage']['name']);
        $uploadFile = $uploadDir . $fileName;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['npcImage']['type'], $allowedTypes) && $_FILES['npcImage']['size'] <= $maxFileSize) {
            if (move_uploaded_file($_FILES['npcImage']['tmp_name'], $uploadFile)) {
                // Delete old image if it exists
                if ($npcImage && file_exists($npcImage)) {
                    unlink($npcImage);
                }
                $npcImage = $uploadFile;
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "Invalid file type or size. Only JPEG, PNG, GIF up to 2MB allowed.";
        }
    }

    if ($npcName && $npcRace && $npcClass && $npcDescription) {
        updateNPC($npcId, $npcName, $npcRace, $npcClass, $npcImage, $npcDescription, $npcIsFriendly);
        header("Location: dm_npcs.php?campaignId=" . $npc['campaignId']);
        exit;
    } else {
        echo "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit NPC</title>
    <style>
        textarea { width: 100%; height: 100px; }
        img { max-width: 100px; height: auto; }
    </style>
</head>
<body>
    <h1>Edit NPC</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="npcName" value="<?php echo htmlspecialchars($npc['npcName']); ?>" required>
        <input type="text" name="npcRace" value="<?php echo htmlspecialchars($npc['npcRace']); ?>" required>
        <input type="text" name="npcClass" value="<?php echo htmlspecialchars($npc['npcClass']); ?>" required>
        <label>Current Image: 
            <?php if ($npc['npcImage']): ?>
                <img src="<?php echo htmlspecialchars($npc['npcImage']); ?>" alt="Current NPC Image">
            <?php else: ?>
                None
            <?php endif; ?>
        </label><br>
        <input type="file" name="npcImage" accept="image/jpeg,image/png,image/gif">
        <textarea name="npcDescription" required><?php echo htmlspecialchars($npc['npcDescription']); ?></textarea>
        <label><input type="checkbox" name="npcIsFriendly" <?php echo $npc['npcIsFriendly'] ? 'checked' : ''; ?>> Is Friendly?</label>
        <button type="submit">Update NPC</button>
    </form>
    <a href="dm_npcs.php?campaignId=<?php echo $npc['campaignId']; ?>">Cancel</a>
</body>
</html>