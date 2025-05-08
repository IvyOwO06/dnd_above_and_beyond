<?php

include "functions.php";

function signup()
{
    $conn = dbConnect();
    if (isset($_POST['submit'])) {
        $username = mysqli_real_escape_string($conn, $_POST['user']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['pass']);
        $cpassword = mysqli_real_escape_string($conn, $_POST['cpass']);

        $sql = "SELECT * FROM user WHERE userName = '$username'";
        $result = mysqli_query($conn, $sql);
        $count_user = mysqli_num_rows($result);

        $sql = "SELECT * FROM user WHERE mail = '$email'";
        $result = mysqli_query($conn, $sql);
        $count_email = mysqli_num_rows($result);

        if ($count_user == 0 && $count_email == 0) {

            if ($password == $cpassword) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user(userName, mail, password) VALUES ('$username', '$email', '$hash')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    echo '<script>
                    alert("thank you for signing up"); 
                    window.location.href= "index.php"
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

function login()
{
    $conn = dbConnect();
    session_start();
    if (isset($_POST["uname"]) && isset($_POST["password"])) {
        $uname = $_POST['uname'];
        $password = $_POST['password'];
    }

    // Trim input to avoid leading/trailing spaces
    $uname = trim($uname);
    $password = trim($password);

    if (empty($uname)) {
        echo '<script>alert("Username is required"); window.history.back();</script>';
        exit();
    } elseif (empty($password)) {
        echo '<script>alert("Password is required"); window.history.back();</script>';
        exit();
    } else {
        $stmt = $conn->prepare("SELECT * FROM user WHERE userName = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = [
                    'username' => $row['username'],
                    'id' => $row['id'],
                ];
                echo '<script>alert("Log in Success!"); window.location.href = "index.php";</script>';
                exit();
            } else {
                echo '<script>alert("Incorrect username or password"); window.history.back();</script>';
                exit();
            }
        } else {
            echo '<script>alert("Incorrect username or password"); window.history.back();</script>';
            exit();
        }
    }
}

?>