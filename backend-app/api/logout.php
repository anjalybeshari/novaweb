<?php
// backend-app/api/logout.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
session_destroy();
echo json_encode(["status"=>"success","message"=>"Logged out"]);
