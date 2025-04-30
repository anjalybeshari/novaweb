<?php
// backend-app/api/products.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';

// optional filters
$category = $_GET['category'] ?? null;
$brand    = $_GET['brand'] ?? null;

$sql = "SELECT * FROM products";
$conds = [];
if ($category) $conds[] = "category_id = " . intval($category);
if ($brand)    $conds[] = "brand_id    = " . intval($brand);
if ($conds)    $sql .= " WHERE " . implode(" AND ", $conds);

$result = $conn->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
  $products[] = $row;
}

echo json_encode($products);
