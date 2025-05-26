<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Preflight OPTIONS përgjigjet menjëherë
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/config.php';
include '../functions/common_function.php';
session_start();

$ip = getIPAddress();
$body = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // ... si ke
        break;

    case 'POST':
        $product_id = $body['product_id'] ?? null;
        $quantity = $body['quantity'] ?? 1;

        if (!$product_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product_id']);
            exit;
        }

        $ok = add_to_cart((int)$product_id, (int)$quantity);

        echo json_encode(['status' => $ok ? 'added' : 'failed']);
        break;

    // rastet e tjera...

    default:
        http_response_code(405);
        echo json_encode(['error'=>'Method not allowed']);
}
