<?php
// backend-app/includes/connect.php

$host = "localhost";
$user = "root";
$password = "Ani.0399";
$database = "NOVA";

// Përdorim $con në vend të $conn për konsistencë me funksionet
$con = new mysqli($host, $user, $password, $database);

if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}
?>
