<?php
// backend-app/api/login.php

// 1) CORS & preflight
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}

header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../config/config.php';

// 2) Lexo input
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["status"=>"error","message"=>"Invalid input"]);
    exit;
}

$email    = $con->real_escape_string($data['email']);
$password = $data['password'];

// 3) Query për user
$stmt = $con->prepare("SELECT id, name, email, role, password FROM User WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["status"=>"error","message"=>"Invalid credentials"]);
    exit;
}

// 4) Para migrate: ruaj session_id i vjetër
$oldSession = session_id();

// 5) Anti-session-fixation & vendos user_id
session_regenerate_id(true);
$newSession = session_id();
$_SESSION['user_id'] = $user['id'];  // **çelësi i saktë**
$_SESSION['name']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

// 6) Migro karrocën anonime në përdorues
$mig = $con->prepare("
    UPDATE Cart
       SET user_id    = ?,
           session_id = ?
     WHERE session_id = ?
");
$mig->bind_param('iss', $user['id'], $newSession, $oldSession);
$mig->execute();

// 7) Jep përgjigjen
echo json_encode([
    "status"  => "success",
    "message" => "Login successful",
    "user"    => [
        "id"    => $user["id"],
        "name"  => $user["name"],
        "email" => $user["email"],
        "role"  => $user["role"]
    ]
]);
exit;
