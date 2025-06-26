<?php
require 'inc/navFunctions.php';
require 'inc/profileFunctions.php';

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
} else {
    header("location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        $ousername = $_SESSION['user']['username'];
        $username = $_POST['username'];
        if ($ousername == $username) {
            echo '<script>
                    alert("Previous username cannot be the same as the new username");
                    window.history.back();
                    </script>';
        } else {
            $conn = dbConnect();
            $stmt = $conn->prepare('UPDATE user SET userName = ? WHERE userId = ?');
            $stmt->execute([$username, $userId]);
            $_SESSION['user']['username'] = $username;
            echo '<script>
                    alert("Your username has been changed");
                    </script>';
        }
    }
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $conn = dbConnect();

        $stmt = $conn->prepare("SELECT * FROM user WHERE userName = ?");
        $stmt->bind_param("s", $_SESSION['user']['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $opassword = $row['password'];

            if ($password === $cpassword) {
                // Check if new password is the same as the old one
                if (password_verify($password, $opassword)) {
                    echo '<script>
                        alert("Previous password cannot be the same as the new password");
                        window.history.back();
                        </script>';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare('UPDATE user SET password = ? WHERE userId = ?');
                    $stmt->bind_param('si', $hash, $userId); // Use bind_param if using mysqli
                    $stmt->execute();

                    echo '<script>
                        alert("Your password has been changed"); 
                        window.location.href= "logout";
                        </script>';
                }
            } else {
                echo '<script>
                    alert("Passwords do not match");
                    window.history.back();
                    </script>';
            }
        }
    }
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $conn = dbConnect();

        $stmt = $conn->prepare("SELECT * FROM user WHERE userName = ?");
        $stmt->bind_param("s", $_SESSION['user']['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $omail = $row['mail'];
            if ($email == $omail) {
                echo '<script>
                        alert("Previous Email cannot be the same as the new Email");
                        window.history.back();
                        </script>';
            } else {
                $conn = dbConnect();
                $stmt = $conn->prepare('UPDATE user SET mail = ? WHERE userId = ?');
                $stmt->execute([$email, $userId]);
                echo '<script>
                        alert("Your Email has been changed");
                        </script>';
            }
        }
    }
    if (isset($_POST['bio'])) {
        $bio = $_POST['bio'];
        $conn = dbConnect();

        $stmt = $conn->prepare('UPDATE user SET profileInformation = ? WHERE userId = ?');
        $stmt->execute([$bio, $userId]);
        echo '<script>
                alert("Your Bio has been changed");
                </script>';
    }
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
    <link rel="stylesheet" href="css/options.css">
</head>

<body>
    <?php
    displayHeader();

    $profile = getProfile($profileId);
?>
<div class="options-container">
    <h2>Profile Settings</h2>   
    <form method="POST">
        <h3>Change Username</h3>
        <input type="text" name="username" placeholder="<?= htmlspecialchars($_SESSION['user']['username']) ?>">
        <input type="submit" value="Update Username" onclick="return confirm('Are you sure you want to change your username?')">
    </form>

    <hr>

    <form method="POST">
        <h3>Change Password</h3>
        <input type="password" name="password" placeholder="New Password" minlength="8">
        <input type="password" name="cpassword" placeholder="Retype New Password" minlength="8">
        <input type="submit" value="Update Password" onclick="return confirm('Are you sure you want to change your password?\nYou will be logged out.')">
    </form>

    <hr>

    <form method="POST">
        <h3>Change Email</h3>
        <input type="email" name="email" placeholder="New Email">
        <input type="submit" value="Update Email" onclick="return confirm('Are you sure you want to change your email?')">
    </form>

    <hr>

    <form method="POST">
        <h3>Change Bio</h3>
        <textarea name="bio"><?= htmlspecialchars($profile['profileInformation']) ?></textarea>
        <button type="submit">Save Bio</button>
    </form>
</div>
</body>
