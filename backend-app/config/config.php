<?php
// backend-app/includes/connect.php

$host = "192.168.1.184";
$user = "ani";
$password = "ani_pw";
$database = "NOVA";
$port = 3306;

$con = new mysqli($host, $user, $password, $database, $port);

// Kontrollo gabimet pasi e ke inicializuar $con
if ($con->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $con->connect_error]));
}
?>
