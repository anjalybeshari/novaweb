<?php

$host = "192.168.1.210";
$user = "olta";
$password = "olta_pw";
$database = "NOVA";
$port = 3306;

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

return $conn;
?>