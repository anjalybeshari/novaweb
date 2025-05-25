<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
require_once '../config/config.php';

$sql = "SELECT product_id, product_title, product_price, product_image1 FROM products";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "products" => $products
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No products found"
    ]);
}

$conn->close();
