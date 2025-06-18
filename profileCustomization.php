<?php
require 'inc/navFunctions.php';
require 'inc/profileFunctions.php';

if (!isset($_SESSION['user'])) {
    header("location: index.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$profileId = $_GET['userId'] ?? $userId;
$db = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['formType'])) {
        die('Missing form type.');
    }

    switch ($_POST['formType']) {
        case 'color':
            $newColor = $_POST['profileColor'];
            $stmt = $db->prepare("UPDATE user SET profileColor = ? WHERE userId = ?");
            $stmt->execute([$newColor, $userId]);
            header("Location: profile.php?userId=$userId");
            exit();

        case 'description':
            $profileInfo = trim($_POST['profileInformation']);
            $stmt = $db->prepare("UPDATE user SET profileInformation = ? WHERE userId = ?");
            $stmt->bind_param('si', $profileInfo, $userId);
            if ($stmt->execute()) {
                header("Location: profile.php?userId=$userId");
                exit();
            } else {
                echo "Error saving description.";
            }
            break;

        // Add other form cases here later (e.g. 'bannerUpload', 'pictureUpload')
        
        default:
            echo "Unknown form submission.";
    }
}

$profile = getProfile($profileId);
$user = getUser($userId);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Options</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php displayHeader(); ?>

    <h2>Profile Options</h2>

    <!-- Profile Picture -->
    <?php if (!empty($user['profilePicture'])): ?>
        <img src="<?= htmlspecialchars($user['profilePicture']) ?>" alt="Profile Picture" width="150">
    <?php else: ?>
        <p>No profile picture uploaded.</p>
    <?php endif; ?>

    <form action="profileOptions.php?userId=<?= $userId ?>" method="POST" enctype="multipart/form-data">
        <h3>Update Profile Picture:</h3>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit">Upload</button>
    </form>

    <br>

    <!-- Profile Banner -->
    <?php if (!empty($user['profileBanner'])): ?>
        <img src="<?= htmlspecialchars($user['profileBanner']) ?>" alt="Profile Banner" width="150">
    <?php else: ?>
        <p>No profile banner uploaded.</p>
    <?php endif; ?>

    <form action="profileOptions.php?userId=<?= $userId ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="formType" value="bannerUpload">
        <label for="banner_image">Upload your banner:</label><br>
        <input type="file" name="banner_image" id="banner_image" accept="image/jpeg,image/png,image/gif" required><br><br>
        <button type="submit">Upload Banner</button>
    </form><?php
require 'inc/navFunctions.php';
require 'inc/profileFunctions.php';

session_start();

if (!isset($_SESSION['user'])) {
    header("location: index.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$profileId = $_GET['userId'] ?? $userId;
$db = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['formType'])) {
        die('Missing form type.');
    }

    switch ($_POST['formType']) {
        case 'color':
            $newColor = $_POST['profileColor'];
            $stmt = $db->prepare("UPDATE user SET profileColor = ? WHERE userId = ?");
            $stmt->execute([$newColor, $userId]);
            header("Location: profile.php?userId=$userId");
            exit();

        case 'description':
            $profileInfo = trim($_POST['profileInformation']);
            $stmt = $db->prepare("UPDATE user SET profileInformation = ? WHERE userId = ?");
            $stmt->bind_param('si', $profileInfo, $userId);
            if ($stmt->execute()) {
                header("Location: profile.php?userId=$userId");
                exit();
            } else {
                echo "Error saving description.";
            }
            break;

        // Add other form cases here later (e.g. 'bannerUpload', 'pictureUpload')
        
        default:
            echo "Unknown form submission.";
    }
}

$profile = getProfile($profileId);
$user = getUser($userId);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Options</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php displayHeader(); ?>

    <h2>Profile Options</h2>

    <!-- Profile Picture -->
    <?php if (!empty($user['profilePicture'])): ?>
        <img src="<?= htmlspecialchars($user['profilePicture']) ?>" alt="Profile Picture" width="150">
    <?php else: ?>
        <p>No profile picture uploaded.</p>
    <?php endif; ?>

    <form action="profileOptions.php?userId=<?= $userId ?>" method="POST" enctype="multipart/form-data">
        <h3>Update Profile Picture:</h3>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit">Upload</button>
    </form>

    <br>

    <!-- Profile Banner -->
    <?php if (!empty($user['profileBanner'])): ?>
        <img src="<?= htmlspecialchars($user['profileBanner']) ?>" alt="Profile Banner" width="150">
    <?php else: ?>
        <p>No profile banner uploaded.</p>
    <?php endif; ?>

    <form action="profileOptions.php?userId=<?= $userId ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="formType" value="bannerUpload">
        <label for="banner_image">Upload your banner:</label><br>
        <input type="file" name="banner_image" id="banner_image" accept="image/jpeg,image/png,image/gif" required><br><br>
        <button type="submit">Upload Banner</button>
    </form>

    <!-- Profile Description -->
    <form method="post">
        <input type="hidden" name="formType" value="description">
        <input type="hidden" name="userId" value="<?= $userId ?>">
        <label for="profileInformation">Profile Description:</label><br>
        <textarea name="profileInformation" id="profileInformation" rows="5" cols="50"><?= htmlspecialchars($profile['profileInformation'] ?? '') ?></textarea><br>
        <button type="submit">Save Description</button>
    </form>

    <!-- Profile Color -->
    <form method="post">
        <input type="hidden" name="formType" value="color">
        <label for="profileColor">Choose profile color:</label>
        <input type="color" id="profileColor" name="profileColor" required>
        <button type="submit">Save</button>
    </form>

    <?php displayFooter(); ?>
</body>
</html>


    <!-- Profile Description -->
    <form method="post">
        <input type="hidden" name="formType" value="description">
        <input type="hidden" name="userId" value="<?= $userId ?>">
        <label for="profileInformation">Profile Description:</label><br>
        <textarea name="profileInformation" id="profileInformation" rows="5" cols="50"><?= htmlspecialchars($profile['profileInformation'] ?? '') ?></textarea><br>
        <button type="submit">Save Description</button>
    </form>

    <!-- Profile Color -->
    <form method="post">
        <input type="hidden" name="formType" value="color">
        <label for="profileColor">Choose profile color:</label>
        <input type="color" id="profileColor" name="profileColor" required>
        <button type="submit">Save</button>
    </form>

    <?php displayFooter(); ?>
</body>
</html>
