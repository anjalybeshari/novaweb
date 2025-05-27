<?php
// backend-app/api/search.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


require_once __DIR__ . '/../config/config.php';

$q = $con->real_escape_string($_GET['q'] ?? '');
$res = $con->query("
  SELECT * 
    FROM products 
   WHERE product_title LIKE '%{$q}%'
      OR product_description LIKE '%{$q}%'
");
$results = [];
while ($r = $res->fetch_assoc()) $results[] = $r;

echo json_encode($results);
