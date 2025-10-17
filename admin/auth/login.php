<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../connect.php';  // ينشئ $con = PDO

// استلام القيم (form أو JSON)
$email    = $_POST['email']    ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
  $raw = json_decode(file_get_contents('php://input'), true);
  if (is_array($raw)) {
    $email    = $email    ?: ($raw['email'] ?? null);
    $password = $password ?: ($raw['password'] ?? null);
  }
}

if (!$email || !$password) {
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'email and password are required']);
  exit;
}

// مطابق للبيانات اللي في جدول admin (sha1)
$passHash = sha1($password);

$stmt = $con->prepare("
  SELECT admin_id, admin_name, admin_email, admin_phone, admin_password, admin_approve,
         created_at, updated_at
  FROM admin
  WHERE admin_email = ?
  LIMIT 1
");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin || $admin['admin_password'] !== $passHash) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'invalid credentials']);
  exit;
}

if ((int)$admin['admin_approve'] !== 1) {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'admin not approved']);
  exit;
}

http_response_code(200);
echo json_encode([
  'ok'   => true,
  'data' => [
    'admin_id'    => (int)$admin['admin_id'],
    'admin_name'  => $admin['admin_name'],
    'admin_email' => $admin['admin_email'],
    'created_at'  => $admin['created_at'],
    'updated_at'  => $admin['updated_at'],
  ]
]);
