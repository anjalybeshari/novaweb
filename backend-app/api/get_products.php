<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';

$sql = "SELECT * FROM products WHERE status = 'true'";
$res = $con->query($sql);

if (!$res) {
    http_response_code(500);
    echo json_encode(["error" => $con->error]);
    exit;
}

$products = [];
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);

