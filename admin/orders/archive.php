<?php
// admin/orders/archive.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../connect.php';

try {
  // خذ id من POST أو GET عبر filterRequest (بعد ما تعدلها تدعم الاثنين)
  $id = filterRequest('id');
  $id = ($id === null || $id === '') ? null : (int)$id;

  if ($id === null || $id <= 0) {
    echo json_encode(["status" => "failure", "message" => "Missing or invalid id"]);
    exit;
  }

  // أرشف الطلب: غيّر الحالة إلى 4 فقط إذا لم تكن 4 أصلاً
  $sql = "UPDATE orders
            SET orders_status = 4, updated_at = NOW()
          WHERE orders_id = :id AND (orders_status IS NULL OR orders_status <> 4)";
  $stmt = $con->prepare($sql);
  $ok   = $stmt->execute([":id" => $id]);

  if (!$ok) {
    $err = $stmt->errorInfo();
    echo json_encode([
      "status"  => "failure",
      "message" => "Update failed",
      "error"   => $err[2] ?? null
    ]);
    exit;
  }

  if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => "success", "data" => ["orders_id" => $id, "orders_status" => 4]]);
  } else {
    // ما في صفوف اتعدّلت: إما الطلب غير موجود، أو أصلاً مؤرشف
    echo json_encode([
      "status"  => "failure",
      "message" => "Order not found or already archived",
      "orders_id" => $id
    ]);
  }
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["status" => "serverException", "message" => $e->getMessage()]);
  exit;
}
