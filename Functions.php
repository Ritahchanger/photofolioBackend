<?php
$database = "photofolio";

function create_connection()
{
    $servername = "localhost";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
function create_database($connection, $database)
{
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    if ($connection->query($sql)) {
        return true;
    } else {
        return false;
    }
}
