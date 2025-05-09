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

function dd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}