<?php

session_start();

function dbConnect()
{
    $serverName = "localhost";
    $username = "root";
    $password = "";
    $dbName = "dnm";

    $conn = new mysqli($serverName, $username, $password, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

if (isset($_SESSION['user']))
{
    function getUser($userId)
    {
        $db = dbConnect();

        $sql = 'SELECT * FROM user WHERE userId =' . $userId;

        $resource = $db->query($sql) or die($db->error);

        $user = $resource->fetch_assoc();

        return $user;
    }
}

function dd($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function timer()
{
    ?>
    <script>
        // Start timer when page loads
        window.onload = function() {
            let seconds = 0;
            let timer = setInterval(function() {
                seconds++;
                
                // Calculate hours, minutes, seconds
                let hrs = Math.floor(seconds / 3600);
                let mins = Math.floor((seconds % 3600) / 60);
                let secs = seconds % 60;
                
                // Format time with leading zeros
                hrs = hrs < 10 ? "0" + hrs : hrs;
                mins = mins < 10 ? "0" + mins : mins;
                secs = secs < 10 ? "0" + secs : secs;
                
            }, 1000); // Update every second
        };
    </script>
    <?php

}

if (basename($_SERVER['PHP_SELF']) === 'functions.php') {
header('location: ../index.php');
}