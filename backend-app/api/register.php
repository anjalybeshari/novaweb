<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();
require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (
    ! $data ||
    empty($data['name']) ||
    empty($data['email']) ||
    empty($data['password'])
) {
    echo json_encode([
      "status"  => "error",
      "message" => "Missing name, email or password"
    ]);
    exit;
}

$name     = $conn->real_escape_string(trim($data['name']));
$email    = $conn->real_escape_string(trim($data['email']));
$rawPw    = $data['password'];
$role     = 'user';  // default for all new registrations

// 1) Check if email already exists
$check = $conn->query("SELECT 1 FROM User WHERE email = '$email'");
if ($check && $check->num_rows > 0) {
    echo json_encode([
      "status"  => "error",
      "message" => "Email already registered"
    ]);
    exit;
}

// 2) Hash and insert
$hash = password_hash($rawPw, PASSWORD_DEFAULT);
$sql  = "INSERT INTO User (name,email,password,role)
         VALUES ('$name','$email','$hash','$role')";

if ($conn->query($sql)) {
    echo json_encode([
      "status"  => "success",
      "message" => "Registration successful"
    ]);
} else {
    echo json_encode([
      "status"  => "error",
      "message" => "Database error: " . $conn->error
    ]);
}

$conn->close();
