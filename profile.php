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
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <?php
    displayHeader();
    ?>
    <?php
    if ($userId == $profileId) {
        ?>
        <h3>Username: <?php echo $user['userName']; ?></h3>
        <h3>Profile Picture:</h3>
        <?php if (!empty($user['profilePicture'])) { ?>
            <img src="<?php echo htmlspecialchars($user['profilePicture']); ?>" alt="Profile Picture" width="150">
            <?php
        } else {
            ?>
            <p>No profile picture uploaded.</p>
            <?php
        }
        ?>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <h3>Update Profile Picture:</h3>
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit" class="normal-button">Upload</button>
        </form>
        <br><br>
        <a href="creations.php?userId=<?php echo $profileId; ?>">Creations</a>
        <?php
    } else {
        $profile = getProfile($profileId);
        ?>
        <h3>Username: <?php echo $profile['userName']; ?></h3>
        <h3>Profile Picture:</h3>
        <?php if (!empty($profile['profilePicture'])) { ?>
            <img src="<?php echo htmlspecialchars($profile['profilePicture']); ?>" alt="Profile Picture" width="150">
            <?php
        } else {
            ?>
            <p>No profile picture uploaded.</p>
            <?php
        }
        ?>
        <br><br>
        <a href="creations.php?userId=<?php echo $profileId; ?>">Creations</a>
        <?php
    }
    $profiles = getProfiles();
    ?>
    <ul>
        <?php
        foreach ($profiles as $profile) {
            ?>
            <li><a href="profile.php?userId=<?php echo $profile['userId']; ?>"><?php echo $profile['userName']; ?></a></li>
            <?php
        }
        ?>
    </ul>
    <?php
    displayFooter();
    ?>
</body>

</html>