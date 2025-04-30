<?php
// backend-app/api/search.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';

$q = $conn->real_escape_string($_GET['q'] ?? '');
$res = $conn->query("
  SELECT * 
    FROM products 
   WHERE product_title LIKE '%{$q}%'
      OR product_description LIKE '%{$q}%'
");
$results = [];
while ($r = $res->fetch_assoc()) $results[] = $r;

echo json_encode($results);
