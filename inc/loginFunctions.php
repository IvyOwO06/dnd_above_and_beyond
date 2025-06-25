<?php

require_once "functions.php";

function signup()
{
    $conn = dbConnect();
    if (isset($_POST['submit'])) {
        $username = mysqli_real_escape_string($conn, $_POST['user']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['pass']);
        $cpassword = mysqli_real_escape_string($conn, $_POST['cpass']);
        $banner = "files/banners/banner_68527a720a7bc1.81326786.jpg";

        $profilePicture = "images/defaultPFP.png";

        $sql = "SELECT * FROM user WHERE userName = '$username'";
        $result = mysqli_query($conn, $sql);
        $count_user = mysqli_num_rows($result);

        $sql = "SELECT * FROM user WHERE mail = '$email'";
        $result = mysqli_query($conn, $sql);
        $count_email = mysqli_num_rows($result);

        if ($count_user == 0 && $count_email == 0) {

            if ($password == $cpassword) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user(userName, mail, password, profilePicture, profileBanner, displayName) VALUES ('$username', '$email', '$hash', '$profilePicture', '$banner', '$username')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    echo '<script>
                    alert("thank you for signing up"); 
                    window.location.href= "index"
                    </script>';
                }
            } else {
                echo '<script>
            alert("Passwords do not match");
            window.history.back();
            </script>';
            }
        } else {
            if ($count_user > 0) {
                echo '<script>
            alert("Username already exists")
            window.history.back();
            </script>';
            }
            if ($count_email > 0) {
                echo '<script>
            alert("E_mail already exists")
            window.history.back();
            </script>';
            }
        }

    }
}

function login() {
    $conn = dbConnect();
    session_start();

    if (!isset($_POST["uname"]) || !isset($_POST["password"])) {
        $error = "Missing username or password.";
        header("Location: login?error=" . urlencode($error));
        exit();
    }

    $uname = trim($_POST['uname']);
    $password = trim($_POST['password']);

    if (empty($uname) || empty($password)) {
        $error = "Username and password are required.";
        header("Location: login?error=" . urlencode($error));
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE userName = ?");
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user'] = [
                'username' => $row['userName'],
                'id' => $row['userId'],
                // '2fa_pending' => true
            ];

            // // Find a valid Python executable
            // $pythonPaths = shell_exec('where python 2>&1');
            // $validPythonPath = null;
            // $paths = explode("\n", trim($pythonPaths));
            // foreach ($paths as $path) {
            //     $path = trim($path);
            //     // Check Microsoft Store alias
            //     if (strpos($path, 'Microsoft\WindowsApps\python.exe') !== false) {
            //         // Only use it if it’s a working Python installation
            //         $output = shell_exec("$path --version 2>&1");
            //         if (strpos($output, 'Python') === 0) {
            //             $validPythonPath = $path;
            //             break;
            //         }
            //     } else if (file_exists($path)) {
            //         $validPythonPath = $path;
            //         break;
            //     }
            // }

            // if (!$validPythonPath) {
            //     unset($_SESSION['pending_2fa']);
            //     $error = "No valid Python installation found. Please install Python 3 from python.org and add it to PATH.";
            //     header("Location: login?error=" . urlencode($error));
            //     exit();
            // }

            // // Use relative path for the script
            // $scriptPath = __DIR__ . "/../scripts/python/send_2fa_code.py";
            // if (!file_exists($scriptPath)) {
            //     unset($_SESSION['pending_2fa']);
            //     $error = "2FA script not found at $scriptPath.";
            //     header("Location: login?error=" . urlencode($error));
            //     exit();
            // }

            // $email = $row['mail'];
            // $username = $row['userName'];
            // $escapedEmail = escapeshellarg($email);
            // $escapedUsername = escapeshellarg($username);
            // $command = "$validPythonPath $scriptPath $escapedEmail $escapedUsername 2>&1";
            // $output = shell_exec($command);

            // // Debug: Log the command and output
            // file_put_contents(__DIR__ . "/../debug.log", "Command: $command\nOutput: $output\n", FILE_APPEND);

            // // Parse Python script output
            // $result = json_decode($output, true);
            // if (!$result || $result['status'] !== 'success') {
            //     unset($_SESSION['pending_2fa']);
            //     $error = "Failed to send 2FA code: " . ($result['message'] ?? 'Unknown error');
            //     header("Location: login?error=" . urlencode($error));
            //     exit();
            // }

            // // Store 2FA code and expiry
            // $_SESSION['pending_2fa']['2fa_code'] = $result['code'];
            // $_SESSION['pending_2fa']['2fa_expiry'] = $result['expiry'];

            // header("Location: verify2fa?user=" . urlencode($username));
            header("Location: index");
            exit();
        }
    }

    $error = "Incorrect username or password.";
    header("Location: login?error=" . urlencode($error));
    exit();
}

?>