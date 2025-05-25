<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';

$sql = "SELECT * FROM products WHERE status = true";
$res = $conn->query($sql);

$products = [];
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
