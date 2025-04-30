<?php
// backend-app/api/brands.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';

$res = $conn->query("SELECT brand_id AS id, brand_title AS name FROM brands");
$brands = [];
while ($r = $res->fetch_assoc()) $brands[] = $r;

echo json_encode($brands);
