<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';  // ku inicializohet $con ose $conn

// optional filters
$category = $_GET['category'] ?? null;
$brand    = $_GET['brand'] ?? null;
$limit    = isset($_GET['limit']) ? intval($_GET['limit']) : null;

$sql = "SELECT * FROM products";
$conds = ["status = 'true'"];

if ($category) {
    $conds[] = "category_id = " . intval($category);
}
if ($brand) {
    $conds[] = "brand_id = " . intval($brand);
}

$sql .= " WHERE " . implode(" AND ", $conds);

if ($limit) {
    $sql .= " LIMIT " . $limit;
}

$result = $con->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
