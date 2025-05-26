<?php
// backend-app/api/get_all_users.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
require_once '../config/config.php';

// Verifiko nëse përdoruesi është admin për të pasur akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  http_response_code(403);
  echo json_encode(["status" => "error", "message" => "Access denied"]);
  exit;
}

$query = "SELECT id, name, email, role FROM User";
$result = $con->query($query);

if ($result && $result->num_rows > 0) {
  $users = [];
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }

  echo json_encode([
    "status" => "success",
    "users" => $users
  ]);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "No users found"
  ]);
}

$con->close();
