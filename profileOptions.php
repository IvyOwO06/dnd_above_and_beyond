<?php
require 'inc/navFunctions.php';
require 'inc/profileFunctions.php';

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
} else {
    header("location: index.php");
}
$profileId = $_GET['userId'];

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
    <?php
    displayHeader();

    $profile = getProfile($profileId);

    if (!empty($user['profilePicture'])) { ?>
        <img src="<?php echo htmlspecialchars($user['profilePicture']); ?>" alt="Profile Picture" width="150">
        <?php
    } else {
        ?>
        <p>No profile picture uploaded.</p>
        <?php
    }
    ?>
    <form action="profileOptions?userId=<?php echo $userId; ?>" method="POST" enctype="multipart/form-data">
        <h3>Update Profile Picture:</h3>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" class="normal-button">Upload</button>
    </form>
    <br>
    <h3>Change Username</h3>
    
    <p>Username: <?php echo $profile['userName']; ?></p>
    <form>

    </form>
    <h3>Change password</h3>
    <h3>Change Email</h3>
    <h3>Change bio</h3>
    <form action="profileOptions?userId=<?php echo $userId ?>" method="POST">
        <textarea><?php echo $profile['profileInformation']; ?></textarea>
        <button type="submit" class="normal-button">submit</button>
    </form>