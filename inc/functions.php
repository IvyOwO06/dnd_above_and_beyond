<?php

function dbConnect()
{
    $serverName = "localhost";
    $username = "root";
    $password = "";
    $dbName = "dnm";

    $conn = new mysqli($serverName, $username, $password, $dbName, 3307);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}