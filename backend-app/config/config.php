<?php

$host = "localhost";
$user = "root";
$password = "Ani.0399";
$database = "NOVA";
// $port = 3306;

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

return $conn;
?>