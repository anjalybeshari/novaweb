<?php
// backend-app/api/categories.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';

$res = $con->query("SELECT category_id AS id, category_title AS name FROM categories");
$cats = [];
while ($r = $res->fetch_assoc()) $cats[] = $r;

echo json_encode($cats);
