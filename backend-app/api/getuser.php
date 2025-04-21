<?php
// backend-app/api/get_user.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
if (!isset($_SESSION['email'])) {
  http_response_code(401);
  echo json_encode(["status"=>"error","message"=>"Not logged in"]);
  exit;
}

echo json_encode([
  "status"=>"success",
  "user"=>[
    "id"    => $_SESSION['user_id'],
    "name"  => $_SESSION['name'],
    "email" => $_SESSION['email'],
    "role"  => $_SESSION['role']
  ]
]);
