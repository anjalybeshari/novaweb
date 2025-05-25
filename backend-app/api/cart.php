<?php
// api/cart.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php'; 
include '../functions/common_function.php';
session_start();

$ip = getIPAddress();

// read raw body for PUT/DELETE
$body = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
  
  case 'GET':
    // fetch cart
    $stmt = $con->prepare("
      SELECT c.product_id, p.product_price, p.product_title, p.product_image, c.quantity
      FROM cart_details c
      JOIN products p ON c.product_id = p.product_id
      WHERE c.ip_address = ?
    ");
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode($result);
    break;
  
  case 'PUT':
    if (!isset($_GET['action']) || $_GET['action']!=='update') {
      http_response_code(400); exit;
    }
    // expect body: { qty: { "1": 2, "4":3 } }
    foreach ($body['qty'] as $id => $qty) {
      $stmt = $con->prepare("
        UPDATE cart_details SET quantity=? 
        WHERE ip_address=? AND product_id=?
      ");
      $stmt->bind_param('isi', $qty, $ip, $id);
      $stmt->execute();
    }
    echo json_encode(['status'=>'ok']);
    break;
  
  case 'DELETE':
    if (!isset($_GET['action']) || $_GET['action']!=='remove') {
      http_response_code(400); exit;
    }
    // expect body: { remove: [1,4,7] }
    foreach ($body['remove'] as $id) {
      $stmt = $con->prepare("
        DELETE FROM cart_details 
        WHERE ip_address=? AND product_id=?
      ");
      $stmt->bind_param('si', $ip, $id);
      $stmt->execute();
    }
    echo json_encode(['status'=>'ok']);
    break;

     case 'POST':
    $product_id = $body['product_id'] ?? null;
    $quantity   = $body['quantity'] ?? 1;

    if (!$product_id) {
      http_response_code(400);
      echo json_encode(['error' => 'Missing product_id']);
      exit;
    }

    require_once __DIR__ . '/../functions/common_function.php';
    $ok = add_to_cart((int)$product_id, (int)$quantity);

    echo json_encode(['status' => $ok ? 'added' : 'failed']);
    break;
  
  default:
    http_response_code(405);
    echo json_encode(['error'=>'Method not allowed']);
}
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// Për OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}