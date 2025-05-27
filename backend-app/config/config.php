<?php
// backend-app/includes/connect.php

$host = "localhost";
$user = "root";
$password = "Anja21/05/18";
$database = "NOVA";
//$port = 3306;

$con = new mysqli($host, $user, $password, $database);

// Kontrollo gabimet pasi e ke inicializuar $con
if ($con->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $con->connect_error]));
}
?>
