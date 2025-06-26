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



// Profile Picture
if (isset($_POST['upload_picture']) && isset($_FILES['profile_picture'])) {
    if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profile_pictures/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $fileName = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
            $stmt = $db->prepare("UPDATE user SET profilePicture = ? WHERE userId = ?");
            $stmt->execute([$uploadPath, $userId]);
            header("Location: profileCustomization.php?userId=$userId");
            exit();
        }
    }
}

// Profile Banner
if (isset($_POST['upload_banner']) && isset($_FILES['banner_image'])) {
    if ($_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/banners/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $fileName = uniqid() . '-' . basename($_FILES['banner_image']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $uploadPath)) {
            $stmt = $db->prepare("UPDATE user SET profileBanner = ? WHERE userId = ?");
            $stmt->execute([$uploadPath, $userId]);
            header("Location: profileCustomization.php?userId=$userId");
            exit();
        }
    }
}

// Description
if (isset($_POST['update_description'])) {
    $profileInfo = trim($_POST['profileInformation']);
    $stmt = $db->prepare("UPDATE user SET profileInformation = ? WHERE userId = ?");
    $stmt->execute([$profileInfo, $userId]);
    header("Location: profileCustomization.php?userId=$userId");
    exit();
}

// Color
if (isset($_POST['update_color'])) {
    $color = $_POST['profileColor'];
    $stmt = $db->prepare("UPDATE user SET profileColor = ? WHERE userId = ?");
    $stmt->execute([$color, $userId]);
    header("Location: profileCustomization.php?userId=$userId");
    exit();
}

// Font
if (isset($_POST['update_font'])) {
    $font = $_POST['profileFont'];
    $stmt = $db->prepare("UPDATE user SET profileFont = ? WHERE userId = ?");
    $stmt->execute([$font, $userId]);
    header("Location: profileCustomization.php?userId=$userId");
    exit();
}

// Dark Mode Toggle
if (isset($_POST['toggle_darkmode'])) {
    $enabled = isset($_POST['darkMode']) ? 1 : 0;
    $stmt = $db->prepare("UPDATE user SET darkMode = ? WHERE userId = ?");
    $stmt->execute([$enabled, $userId]);
    header("Location: profileCustomization.php?userId=$userId");
    exit();
}

// Visibility
if (isset($_POST['update_visibility'])) {
    $visibility = $_POST['visibility'] === 'private' ? 'private' : 'public';
    $stmt = $db->prepare("UPDATE user SET profileVisibility = ? WHERE userId = ?");
    $stmt->execute([$visibility, $userId]);
    header("Location: profileCustomization.php?userId=$userId");
    exit();
}

// Display Name
if (isset($_POST['update_displayName'])) {
    $displayName = trim($_POST['displayName']);
    // Optionally sanitize/validate here, e.g. limit length
    if (strlen($displayName) > 50) {
        $displayName = substr($displayName, 0, 50);
    }
    $stmt = $db->prepare("UPDATE user SET displayName = ? WHERE userId = ?");
    $stmt->bind_param("si", $displayName, $userId);
    $stmt->execute();
    header("Location: profileCustomization.php?userId=$userId");
    exit();
}


// Refresh user
$user = getUser($userId);
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Profile Customization</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/options.css">
    <?php displayHeader(); ?>
</head>
<body>

<div class="profile-container">
  <h2>Profile Options</h2>

 <!-- Display Name -->
<form method="POST">
  <h3>Display Name:</h3>
  <input type="text" name="displayName" value="<?= htmlspecialchars($user['displayName'] ?? '') ?>" maxlength="50" placeholder="Your display name">
  <button type="submit" name="update_displayName">Save Display Name</button>
</form>
<hr>

<!-- Profile Picture -->
<form method="POST" enctype="multipart/form-data">
  <h3>Profile Picture:</h3>
  <?php if (!empty($user['profilePicture'])): ?>
    <img src="<?= htmlspecialchars($user['profilePicture']) ?>" class="profile-pic">
  <?php endif; ?>
  <input type="file" name="profile_picture" accept="image/*" required>
  <button type="submit" name="upload_picture">Upload</button>
</form>
<hr>

<!-- Profile Banner -->
<form method="POST" enctype="multipart/form-data">
  <h3>Profile Banner:</h3>
  <?php if (!empty($user['profileBanner'])): ?>
    <img src="<?= htmlspecialchars($user['profileBanner']) ?>" class="profile-banner-preview">
  <?php endif; ?>
  <input type="file" name="banner_image" accept="image/*" required>
  <button type="submit" name="upload_banner">Upload Banner</button>
</form>
<hr>

<!-- Description -->
<form method="POST">
  <h3>Description:</h3>
  <textarea name="profileInformation" rows="4"><?= htmlspecialchars($user['profileInformation'] ?? '') ?></textarea>
  <button type="submit" name="update_description">Save Description</button>
</form>
<hr>

<!-- Color -->
<form method="POST">
  <h3>Profile Color:</h3>
  <div class="color-picker-wrapper">
    <label for="profileColor">Choose a color:</label>
    <input type="color" id="profileColor" name="profileColor" value="<?= htmlspecialchars($user['profileColor'] ?? '#000000') ?>" required>
  </div>
  <button type="submit" name="update_color">Save Color</button>
</form>
  </div>
  <?php displayFooter(); ?>

</body>
</html>
