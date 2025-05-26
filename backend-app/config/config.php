<?php
// backend-app/includes/connect.php

$host = "192.168.1.184";
$user = "altea";
$password = "altea_pw";
$database = "NOVA";
$port = 3306;

// Përdorim $con në vend të $conn për konsistencë me funksionet
$con = new mysqli($host, $user, $password, $database, $port);

if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}
?>
