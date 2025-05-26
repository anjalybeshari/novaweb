<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'User not authenticated']);
        http_response_code(401);
        exit;
    }

    $userId = $_SESSION['user_id'];

    // Konektimi me databazën
    $conn = new mysqli('localhost', 'root', '', 'emri_database_tende');

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    // Merr produktet nga shporta për këtë user
    $result = $conn->query("SELECT * FROM cart WHERE user_id = $userId");
    $items = [];
    $totalPrice = 0;

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $totalPrice += $row['price'] * $row['quantity'];
    }

    if (empty($items)) {
        echo json_encode(['error' => 'Cart is empty']);
        exit;
    }

    // Ruaj porosinë
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("id", $userId, $totalPrice);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    // Ruaj artikujt e porosisë
    foreach ($items as $item) {
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmtItem->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
        $stmtItem->execute();
    }

    // Fshi shportën
    $conn->query("DELETE FROM cart WHERE user_id = $userId");

    echo json_encode(['message' => 'Checkout completed successfully', 'order_id' => $orderId]);
    $conn->close();
}
?>
