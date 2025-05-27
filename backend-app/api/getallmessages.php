<?php
// backend-app/api/getallmessages.php

// 1) CORS & preflight
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// (Vetëm për debug lokal, mund t’i hiqni pas fiksimit)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2) JSON response & session
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../config/config.php';  // ai që bën $con = new mysqli…

// 3) Kontroll roli – vetëm admin
if (!isset($_SESSION['user_id']) || strcasecmp($_SESSION['role'],'admin') !== 0) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Forbidden"]);
    exit;
}

// 4) Përgatitimi i SQL-it
$sql = "
    SELECT id, user_id, content, created_at
      FROM messages
  ORDER BY created_at DESC
";

// 5) Krijo statement-in
$stmt = $con->prepare($sql);

// 6) **Debug: kontroll pas prepare()**
if ($stmt === false) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "DB prepare failed: " . $con->error,
        "sql"     => $sql
    ]);
    exit;
}

// 7) Ekzekuto dhe merr rezultatet
$stmt->execute();
$res  = $stmt->get_result();
$msgs = $res->fetch_all(MYSQLI_ASSOC);

// 8) Kthe JSON-in e pastër
echo json_encode(["status" => "success", "messages" => $msgs]);
