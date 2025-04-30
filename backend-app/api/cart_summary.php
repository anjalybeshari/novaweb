<?php
// backend-app/api/cart_summary.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/common_function.php';

echo json_encode([
  'itemCount'  => cart_item_count(),
  'totalPrice' => total_cart_price()
]);
