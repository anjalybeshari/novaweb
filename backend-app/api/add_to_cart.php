<?php
// backend-app/api/add_to_cart.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/common_function.php';
session_start();

$body = json_decode(file_get_contents("php://input"), true);
$ip = getIPAddress();

if (!isset($body['product_id'])) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => 'Product ID missing']);
  exit;
}

$productId = $body['product_id'];

// Shto në cart ose rrit sasinë nëse ekziston
$stmt = $conn->prepare("
  INSERT INTO cart_details (product_id, ip_address, quantity)
  VALUES (?, ?, 1)
  ON DUPLICATE KEY UPDATE quantity = quantity + 1
");
$stmt->bind_param('is', $productId, $ip);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
