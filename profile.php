<?php
require 'inc/navFunctions.php';
require 'inc/profileFunctions.php';

$profileId = $_GET['userId'];

if (isset($_SESSION['user'])) {
    $user = getUser($userId);
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <?php
    displayHeader();
    $profile = getProfile($profileId);
    if (isset($_SESSION['user'])) {
        if ($userId == $profileId) {
            ?>
                <a href="profileOptions.php?userId=<?php echo $userId ?>">options</a>
            <?php
        }
    }
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