<?php
// api/cart.php

// 1) CORS & JSON headers
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2) Bootstrap + session
require_once __DIR__ . '/../config/config.php';      
include    __DIR__ . '/../functions/common_function.php'; 
session_start();

// 3) Identifikimi i karrocës
$session_id = session_id();
$user_id    = $_SESSION['user_id'] ?? null;

// 4) Ndihmëse për Cart
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
function createCart($con, $user_id, $session_id) {
    if ($user_id) {
        $stmt = $con->prepare("INSERT INTO Cart(user_id, session_id) VALUES(?,?)");
        $stmt->bind_param('is', $user_id, $session_id);
    } else {
        $stmt = $con->prepare("INSERT INTO Cart(session_id) VALUES(?)");
        $stmt->bind_param('s', $session_id);
    }
    $stmt->execute();
    return $stmt->insert_id;
}

// 5) Siguro që kemi cart_id
$cart_id = findCartId($con, $user_id, $session_id);
if (! $cart_id) {
    $cart_id = createCart($con, $user_id, $session_id);
}

// 6) Lexo trupin e request
$body = json_decode(file_get_contents('php://input'), true);

// 7) CRUD me switch mbi metodën HTTP
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $stmt = $con->prepare("
      SELECT ci.item_id   AS product_id,
             p.product_title,
             p.product_image,
             p.product_price,
             ci.quantity
      FROM CartItem ci
      JOIN products p ON ci.item_id = p.product_id
      WHERE ci.cart_id = ?
    ");
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    break;

  case 'POST':
    $product_id = $body['product_id'] ?? null;
    $quantity   = $body['quantity']   ?? 1;
    if (! $product_id) {
      http_response_code(400);
      echo json_encode(['error'=>'Missing product_id']);
      exit;
    }
    // Kontrollo nëse ekziston
    $chk = $con->prepare(
      "SELECT quantity FROM CartItem WHERE cart_id=? AND item_id=?"
    );
    $chk->bind_param('ii', $cart_id, $product_id);
    $chk->execute();
    $row = $chk->get_result()->fetch_assoc();

    if ($row) {
      $newQty = $row['quantity'] + $quantity;
      $upd = $con->prepare(
        "UPDATE CartItem SET quantity=? WHERE cart_id=? AND item_id=?"
      );
      $upd->bind_param('iii', $newQty, $cart_id, $product_id);
      $upd->execute();
    } else {
      $ins = $con->prepare(
        "INSERT INTO CartItem(cart_id, item_id, quantity) VALUES(?,?,?)"
      );
      $ins->bind_param('iii', $cart_id, $product_id, $quantity);
      $ins->execute();
      $newQty = $quantity;
    }

    echo json_encode(['status'=>'added','quantity'=>$newQty]);
    break;

  case 'PUT':
    if (!($_GET['action'] ?? '')==='update') {
      http_response_code(400); exit;
    }
    foreach ($body['qty'] ?? [] as $item_id => $qty) {
      $u = $con->prepare(
        "UPDATE CartItem SET quantity=? WHERE cart_id=? AND item_id=?"
      );
      $u->bind_param('iii', $qty, $cart_id, $item_id);
      $u->execute();
    }
    echo json_encode(['status'=>'ok']);
    break;

  case 'DELETE':
    if (!($_GET['action'] ?? '')==='remove') {
      http_response_code(400); exit;
    }
    foreach ($body['remove'] ?? [] as $item_id) {
      $d = $con->prepare(
        "DELETE FROM CartItem WHERE cart_id=? AND item_id=?"
      );
      $d->bind_param('ii', $cart_id, $item_id);
      $d->execute();
    }
    echo json_encode(['status'=>'ok']);
    break;

  default:
    http_response_code(405);
    echo json_encode(['error'=>'Method not allowed']);
    break;
}

exit;
