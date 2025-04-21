<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();
require_once '../config/config.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$email = $conn->real_escape_string($data->email);
$password = $data->password;

$result = $conn->query("SELECT * FROM User WHERE email = '$email'");

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user" => [
                "id" => $user["id"],
                "name" => $user["name"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$conn->close();
?>
