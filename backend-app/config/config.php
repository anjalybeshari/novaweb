<?php

$host = "localhost";
$user = "root";
$password = "Anja21/05/18";
$database = "NOVA";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}
return $conn;
?>