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

if (basename($_SERVER['PHP_SELF']) === 'functions.php') {
header('location: ../index.php');
}