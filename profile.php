<?php
require 'inc/navFunctions.php';
require 'inc/profileFunctions.php';
require 'inc/creationsFunctions.php';
require 'inc/campaignFunctions.php';


$profileId = $_GET['userId'];

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newColor = $_POST['profileColor'];
    $db = dbconnect();

    $stmt = $db->prepare("UPDATE user SET profileColor = ? WHERE userId = ?");
    $stmt->execute([$newColor, $userId]);

    header("Location: profile.php?userId=$userId");
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>

    <?php
    displayHeader();
    $profile = getProfile($profileId);
    

    ?>        
        <?php if (!empty($profile['profileBanner'])) { ?>
            <img src="<?php echo htmlspecialchars($profile['profileBanner']); ?>" class="profile-banner">
            <?php 
        } else {
            ?>
                <img src="https://placehold.jp/900x300.png">
            <?php
        }
        ?>

    <?php 
        if (!empty($profile['profilePicture'])) 
        { ?>
            <img src="<?php echo htmlspecialchars($profile['profilePicture']); ?>" alt="Profile Picture" class="profile-picture">
            <?php
        } else 
        {
            ?>
            <p>No profile picture uploaded.</p>
            <?php
        }
    
        if (isset($_SESSION['user'])) {
            if ($userId == $profileId) {
                ?>
                    <a href="profileOptions.php?userId=<?php echo $userId ?>" class="btn-options" >options</a>
                    <a href="profileCustomization.php?userId=<?php echo $userId ?>" class="btn-customize">customize</a>
                    <br>
                <?php
            }
        }
    ?>

<body style="background-color: <?php echo htmlspecialchars($profile['profileColor']); ?>">



    <h3 class="username"><?php echo $profile['userName']; ?></h3>

    <?php if (!empty($profile['profileInformation'])): ?>
        <div class="description-container">
    <textarea name="profileInformation"
              rows="5"
              class="profile-description"
              placeholder="Tell the world who you are..."><?= htmlspecialchars($user['profileInformation'] ?? '') ?></textarea>
</div>
<hr>
<div class="campaigns">
    <h2>Campaigns</h2>
    <?php
    ?>
</div>
<div class="creations-section">
    <?php endif; ?>

    <br><br>
    <a href="creations.php?userId=<?php echo $profileId; ?>" class="creations-btn">Creations</a>
    <?php

    displayCharacters($userId, 3);
    ?>
    </div>
<?php
    displayFooter();
    ?>
</body>

</html>