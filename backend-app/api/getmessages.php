<?php
// backend-app/api/get_messages.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(["status"=>"error","message"=>"Not logged in"]);
  exit;
}

$stmt = $conn->prepare(
  "SELECT id, content, created_at 
     FROM messages 
    WHERE user_id = ? 
 ORDER BY created_at DESC"
);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();

$msgs = [];
while ($row = $res->fetch_assoc()) {
  $msgs[] = $row;
}
echo json_encode(["status"=>"success","messages"=>$msgs]);
