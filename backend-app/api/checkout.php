<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'] )) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$userId = $_SESSION['user_id']= 3;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/common_function.php';

// Konektimi me databazën përmes config.php (supozim që e përmban $conn ose $con)
global $con; // Nëse në config.php lidhja ruhet në $con ose $conn, përshtat sipas rastit

// Merr artikujt nga cartitem + cart + products
$sql = "
SELECT ci.cart_item_id, ci.cart_id, ci.item_id, ci.quantity, p.product_title, p.product_price, p.product_image
FROM cartitem ci
JOIN cart c ON ci.cart_id = c.cart_id
JOIN products p ON ci.item_id = p.product_id
WHERE c.user_id = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$totalPrice = 0;

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $totalPrice += $row['product_price'] * $row['quantity'];
}


if (empty($items)) {
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// Ruaj porosinë në databazë me status "pending"
$stmtOrder = $con->prepare("INSERT INTO orders (user_id, total_amount, status, order_date) VALUES (?, ?, 'pending', NOW())");
$stmtOrder->bind_param("id", $userId, $totalPrice);
$stmtOrder->execute();
$orderId = $stmtOrder->insert_id;


// Ruaj artikujt e porosisë
$stmtItem = $con->prepare("INSERT INTO orderitem (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
$stmtItem->bind_param("iiid", $orderId, $item['item_id'], $item['quantity'], $item['product_price']);
    $stmtItem->execute();
}

// Fshi karrocën e përdoruesit
$stmtDeleteCart = $con->prepare("DELETE c, ci FROM cart c LEFT JOIN cartitem ci ON c.cart_id = ci.cart_id WHERE c.user_id = ?");
$stmtDeleteCart->bind_param("i", $userId);
$stmtDeleteCart->execute();

// Funksion për të marrë PayPal Access Token nga sandbox
function getPayPalAccessToken() {
    $clientId = 'ASVrMWQWgAdUiqOR-B4PsInldzX7_j1g7dcpmAXI33YAUu2lYQclBfOnAe16TpKYWlv7mftEXBofhkag';
    $secret = 'EOsD8yBGe1vlEqs5V07l6du-7GCGrswg1_6EYS-SFIvMCceRNG47vokuDMIr_cXsCsUeM7mXNtMEBtKp';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);

    $json = json_decode($response);
    if (isset($json->error)) {
        echo json_encode(['error' => 'PayPal error: ' . $json->error_description]);
        exit;
    }
    return $json->access_token ?? null;
}


// Funksion për të krijuar porosi në PayPal sandbox
function createPayPalOrder($accessToken, $totalPrice) {
    $orderData = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => 'USD',
                'value' => number_format($totalPrice, 2, '.', '')
            ]
        ]],
        'application_context' => [
            'return_url' => 'http://localhost:4200/payment-success',
            'cancel_url' => 'http://localhost:4200/payment-cancel'
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$accessToken = getPayPalAccessToken();
if (!$accessToken) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get PayPal access token']);
    exit;
}

$paypalOrder = createPayPalOrder($accessToken, $totalPrice);

// Debug: regjistro përgjigjen PayPal për verifikim
file_put_contents('paypal_debug.log', print_r($paypalOrder, true));

if (isset($paypalOrder['id'])) {
    $approveLink = null;
    foreach ($paypalOrder['links'] as $link) {
        if ($link['rel'] === 'approve') {
            $approveLink = $link['href'];
            break;
        }
    }

    if (!$approveLink) {
        http_response_code(500);
        echo json_encode(['error' => 'PayPal approve link not found', 'paypal_response' => $paypalOrder]);
        exit;
    }

    echo json_encode([
        'message' => 'Checkout initialized',
        'order_id' => $orderId,
        'paypal_order_id' => $paypalOrder['id'],
        'approve_link' => $approveLink
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create PayPal order', 'paypal_response' => $paypalOrder]);
}

$con->close();
