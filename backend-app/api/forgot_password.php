<?php
// backend-app/api/forgot_password.php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
header("Content-Type: application/json");

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/mailer.php';

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['email'])) {
  echo json_encode(["status"=>"error","message"=>"Email required"]);
  exit;
}

$email = $con->real_escape_string(trim($data['email']));
$res = $con->query("SELECT id FROM User WHERE email='$email'");
if (!$res || $res->num_rows === 0) {
  echo json_encode(["status"=>"error","message"=>"Email not found"]);
  exit;
}

$token      = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry     = date("Y-m-d H:i:s", time() + 3600);

$stmt = $con->prepare(
  "UPDATE User SET reset_token_hash=?, reset_token_expires_at=? WHERE email=?"
);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

// Build reset link
$resetLink = "http://localhost:4200/reset-password?token={$token}";

// Send email (using your mailer.php setup)
$mail->addAddress($email);
$mail->Subject = "Password Reset Request";
$mail->isHTML(true);

// 2) set an HTML body with a clickable link
$mail->Body = "
    <p>Click here to reset your password:</p>
    <p><a href=\"{$resetLink}\">Reset your password</a></p>
";
if ($mail->send()) {
  echo json_encode(["status"=>"success","message"=>"Reset link sent"]);
} else {
  echo json_encode([
    "status"=>"error",
    "message"=>"Mailer error: " . $mail->ErrorInfo
  ]);
}
