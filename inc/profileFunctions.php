<?php

require_once 'functions.php';

function getProfiles()
{
    $db = dbConnect();

    $sql = 'SELECT * FROM user';

    $resource = $db->query($sql) or die($db->error);

    $profiles = $resource->fetch_all(MYSQLI_ASSOC);

    return $profiles;
}

function getProfile($profileId)
{
    $db = dbConnect();

    $sql = 'SELECT *FROM user WHERE userId =' . $profileId;

    $resource = $db->query($sql) or die($db->error);

    $profile = $resource->fetch_assoc();

    return $profile;
}

// profile picture
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbConnect(); // Should return a mysqli connection

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];

        // Step 1: Validate file type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileTmpPath);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mimeType, $allowedTypes)) {
            $error_message = "Only JPG, PNG, and GIF files are allowed.";
            return;
        }

        // Step 2: Check max file size (e.g., 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            $error_message = "The file is too large. Maximum size is 2MB.";
            return;
        }

        // Step 3: Set upload directory
        $uploadDir = 'files/profile_pics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Step 4: Generate unique file name
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
        $fileDestination = $uploadDir . $newFileName;

        // Step 5: Get current profile picture path from database
        $stmt = $db->prepare('SELECT profilePicture FROM user WHERE userId = ?');
        $stmt->bind_param('i', $_SESSION['user']['id']);
        $stmt->execute();
        $stmt->bind_result($currentPicture);
        $stmt->fetch();
        $stmt->close();

        // Step 6: Delete old picture (if not default or empty)
        if ($currentPicture && file_exists($currentPicture) && strpos($currentPicture, 'default') === false) {
            unlink($currentPicture);
        }

        // Step 7: Move the new uploaded file
        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
            // Step 8: Update database with new file path
            $updateStmt = $db->prepare('UPDATE user SET profilePicture = ? WHERE userId = ?');
            $updateStmt->bind_param('si', $fileDestination, $_SESSION['user']['id']);

            if ($updateStmt->execute()) {
                $success_message = "Profile picture updated successfully.";
            } else {
                $error_message = "Failed to update the profile picture in the database.";
            }

            $updateStmt->close();
        } else {
            $error_message = "There was an error moving the uploaded file.";
        }
    } else {
        $error_message = "No file was uploaded or an upload error occurred.";
    }
}

// bio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbConnect();
}
