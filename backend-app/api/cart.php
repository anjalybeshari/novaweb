<?php
// backend-app/api/cart.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/commonfunctions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // fetch summary
    echo json_encode([
      'items' => cart_item_list(),
      'count' => cart_item_count(),
      'total' => total_cart_price()
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    add_to_cart((int)$data['product_id'], (int)($data['quantity'] ?? 1));
    echo json_encode(['status'=>'success']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $q);
    remove_from_cart((int)$q['product_id']);
    echo json_encode(['status'=>'success']);
    exit;
}
