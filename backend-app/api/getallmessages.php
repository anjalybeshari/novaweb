<?php
// backend-app/api/get_all_messages.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin') {
  http_response_code(403);
  echo json_encode(["status"=>"error","message"=>"Forbidden"]);
  exit;
}

$sql = "
  SELECT m.id, m.content, m.created_at, u.name AS user_name
    FROM messages m
    JOIN User u ON u.id = m.user_id
 ORDER BY m.created_at DESC
";
$res = $conn->query($sql);

$msgs = [];
while ($row = $res->fetch_assoc()) {
  $msgs[] = $row;
}
echo json_encode(["status"=>"success","messages"=>$msgs]);
