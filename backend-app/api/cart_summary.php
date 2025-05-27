<?php
// backend-app/api/cart_summary.php

// 1) CORS & preflight
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2) Bootstrap + session
require_once __DIR__ . '/../config/config.php';    // $con
session_start();

// 3) Identifikimi i karrocës
$session_id = session_id();
$user_id    = $_SESSION['user_id'] ?? null;

function findCartId($con, $user_id, $session_id) {
    // 1) Nëse përdoruesi është i kyçur, kërko karrocë me user_id
    if ($user_id) {
        $stmt = $con->prepare("SELECT cart_id FROM Cart WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        if ($row = $stmt->get_result()->fetch_assoc()) {
            return $row['cart_id'];
        }
        // 2) Nëse nuk ka, shiko nëse ka karrocë si anonim (session_id)
        $stmt = $con->prepare("SELECT cart_id FROM Cart WHERE session_id = ?");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        if ($row = $stmt->get_result()->fetch_assoc()) {
            // 3) Lidh atë karrocë me user_id (migrim)
            $cart_id = $row['cart_id'];
            $upd = $con->prepare("UPDATE Cart SET user_id = ? WHERE cart_id = ?");
            $upd->bind_param('ii', $user_id, $cart_id);
            $upd->execute();
            return $cart_id;
        }
        // 4) Nëse s’ka asnjë, do të krijohet më poshtë
        return null;
    }

    // 5) Nëse vizitor anonim (nuk është i kyçur), marr karrocën sipas session_id
    $stmt = $con->prepare("SELECT cart_id FROM Cart WHERE session_id = ?");
    $stmt->bind_param('s', $session_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row['cart_id'] ?? null;
}

$cart_id = findCartId($con, $user_id, $session_id);

// 4) Nëse nuk ka karrocë, kthe zeros
if (!$cart_id) {
    echo json_encode(['itemCount' => 0, 'totalPrice' => 0.0]);
    exit;
}

// 5) Llogarit totalet direkt nga CartItem + products
$sql = "
  SELECT
    COALESCE(SUM(ci.quantity),0)                  AS itemCount,
    COALESCE(SUM(ci.quantity * p.product_price),0) AS totalPrice
  FROM CartItem ci
  JOIN products p ON ci.item_id = p.product_id
  WHERE ci.cart_id = ?
";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $cart_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

// 6) Kthe JSON-in e duhur
echo json_encode([
  'itemCount'  => (int)   $row['itemCount'],
  'totalPrice' => (float) $row['totalPrice']
]);
