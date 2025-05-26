<?php
// backend-app/api/reset_password.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
header("Content-Type: application/json");

session_start();
require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (
   empty($data['token']) ||
   empty($data['password']) ||
   empty($data['password_confirmation'])
) {
  echo json_encode(["status"=>"error","message"=>"All fields required"]);
  exit;
}
if ($data['password'] !== $data['password_confirmation']) {
  echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
  exit;
}

$token_hash = hash("sha256", $data['token']);

$stmt = $con->prepare(
  "SELECT id, reset_token_expires_at
     FROM User
    WHERE reset_token_hash = ?"
);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["status"=>"error","message"=>"Invalid token"]);
  exit;
}

$user = $res->fetch_assoc();
if (strtotime($user['reset_token_expires_at']) < time()) {
  echo json_encode(["status"=>"error","message"=>"Token expired"]);
  exit;
}

// Update password and clear token
$newHash = password_hash($data['password'], PASSWORD_DEFAULT);
$upd = $con->prepare(
  "UPDATE User
      SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL
    WHERE id = ?"
);
$upd->bind_param("si", $newHash, $user['id']);

if ($upd->execute()) {
  echo json_encode(["status"=>"success","message"=>"Password updated"]);
} else {
  echo json_encode(["status"=>"error","message"=>"DB error"]);
}
