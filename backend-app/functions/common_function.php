<?php
// functions/commonfunctions.php

/**
 * Get the client’s IP address (used to key the cart).
 */

global $con;
require_once __DIR__ . '/../config/config.php';


function getIPAddress(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Add a product to the cart (or increment its quantity).
 */
function add_to_cart(int $product_id, int $quantity = 1): bool {
    global $con;
    $ip = getIPAddress();

    // Check if already in cart
    $stmt = $con->prepare(
      "SELECT quantity 
         FROM cart_details 
        WHERE ip_address = ? 
          AND product_id = ?"
    );
    $stmt->bind_param("si", $ip, $product_id);

    // Initialize the variable so fetch() always writes into it
    $existingQty = 0;
    $stmt->bind_result($existingQty);

    $stmt->execute();

    if ($stmt->fetch()) {
        $stmt->close();
        $newQty = $existingQty + $quantity;
        $upd = $con->prepare(
          "UPDATE cart_details 
              SET quantity = ? 
            WHERE ip_address = ? 
              AND product_id = ?"
        );
        $upd->bind_param("isi", $newQty, $ip, $product_id);
        $res = $upd->execute();
        $upd->close();
        return $res;
    } 
    $stmt->close();

    $ins = $con->prepare(
      "INSERT INTO cart_details (product_id, ip_address, quantity)
       VALUES (?, ?, ?)"
    );
    $ins->bind_param("isi", $product_id, $ip, $quantity);
    $res = $ins->execute();
    $ins->close();
    return $res;
}

/**
 * Remove a product entirely from the cart.
 */
function remove_from_cart(int $product_id): bool {
    global $con;
    $ip = getIPAddress();

    $stmt = $con->prepare(
      "DELETE FROM cart_details 
             WHERE ip_address = ? 
               AND product_id = ?"
    );
    $stmt->bind_param("si", $ip, $product_id);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}

/**
 * Return the number of items in the cart (sum of quantities).
 */
function cart_item_count(): int {
    global $con;
    $ip = getIPAddress();

    $stmt = $con->prepare(
      "SELECT COALESCE(SUM(quantity),0) AS total_qty 
         FROM cart_details 
        WHERE ip_address = ?"
    );
    $stmt->bind_param("s", $ip);

    // Initialize before fetch
    $totalQty = 0;
    $stmt->bind_result($totalQty);

    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    return (int)$totalQty;
}

/**
 * Return the total price of all items in the cart.
 */
function total_cart_price(): float {
    global $con;
    $ip = getIPAddress();

    $stmt = $con->prepare(
      "SELECT c.quantity, p.product_price 
         FROM cart_details c
         JOIN products p 
           ON c.product_id = p.product_id
        WHERE c.ip_address = ?"
    );
    $stmt->bind_param("s", $ip);

    // Initialize before fetch
    $qty   = 0;
    $price = 0.0;
    $stmt->bind_result($qty, $price);

    $stmt->execute();

    $total = 0.0;
    while ($stmt->fetch()) {
        $total += $qty * (float)$price;
    }
    $stmt->close();

    return $total;
}

/**
 * Return a detailed list of cart items.
 */
function cart_item_list(): array {
    global $con;
    $ip = getIPAddress();
    $items = [];

    $stmt = $con->prepare(
      "SELECT c.product_id, c.quantity, p.product_title, p.product_price
         FROM cart_details c
         JOIN products p 
           ON c.product_id = p.product_id
        WHERE c.ip_address = ?"
    );
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();

    return $items;

    echo cart_item_count(); // do duhet të kthejë një numër
}
