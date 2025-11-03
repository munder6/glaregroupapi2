<?php
/**
 * orders/checkout.php
 * إنشاء طلب جديد + ربط السلة (بدون إنشاء سجل في delivery)
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../connect.php';

try {
  // ===== 1) قراءة وتطبيع =====
  $usersid        = (int) (filterRequest('usersid') ?? 0);
  $addressidRaw   = filterRequest('addressid');
  $orderstype     = (int) (filterRequest('orderstype') ?? 0); // 1=استلام، 2=توصيل
  $pricedelivery  = (float) (filterRequest('pricedelivery') ?? 0);
  $ordersprice    = (float) (filterRequest('ordersprice') ?? 0);
  $couponid       = trim((string) (filterRequest('couponid') ?? ''));
  $paymentmethod  = (int) (filterRequest('paymentmethod') ?? 0);
  $coupondiscount = (float) (filterRequest('coupondiscount') ?? 0);

  if ($usersid <= 0) {
    http_response_code(400);
    echo json_encode(["status"=>"failure","message"=>"Invalid usersid"]); exit;
  }

  // إن جاك 0 من العميل، خلّيه 2 (توصيل) كافتراضي
  if ($orderstype !== 1 && $orderstype !== 2) $orderstype = 2;

  // العنوان: ممكن يكون NULL (خاصّة لو استلام من المتجر)
  $addressid = ($addressidRaw === '' || $addressidRaw === null) ? null : (int)$addressidRaw;

  // الاستلام من المتجر → بدون أجرة توصيل
  if ($orderstype === 1) $pricedelivery = 0.0;

  // ===== 2) كوبون + إجمالي =====
  $totalprice = $ordersprice + $pricedelivery;
  $couponIdDb = null;

  if ($couponid !== '' && $couponid !== '0') {
    $now = date('Y-m-d H:i:s');
    $stmt = $con->prepare("SELECT coupon_id FROM coupon
                           WHERE coupon_id = :id AND coupon_expdate > :now
                             AND coupon_count > 0 AND coupon_active = '1' LIMIT 1");
    $stmt->execute([":id"=>$couponid,":now"=>$now]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      $couponIdDb = (int)$couponid;
      $totalprice = $totalprice - ($ordersprice * $coupondiscount / 100.0);
      $stmt = $con->prepare("UPDATE coupon SET coupon_count = coupon_count - 1 WHERE coupon_id = :id");
      $stmt->execute([":id"=>$couponIdDb]);
    } else {
      $couponIdDb = null;
      $coupondiscount = 0.0;
    }
  } else {
    $couponIdDb = null;
    $coupondiscount = 0.0;
  }

  // ===== 3) إدخال الطلب (orders_delivery = NULL دائماً) =====
  $nowTs = date('Y-m-d H:i:s');

  $sqlOrder = "INSERT INTO orders
      (orders_usersid, orders_address, orders_type, orders_pricedelivery,
       orders_price, orders_coupon, orders_totalprice, orders_paymentmethod,
       orders_status, orders_rating, orders_noterating, orders_delivery,
       created_at, updated_at)
    VALUES
      (:usersid, :address, :type, :pricedelivery,
       :price, :coupon, :total, :paymethod,
       :status, :rating, :noterating, :delivery,
       :created, :updated)";
  $stmt = $con->prepare($sqlOrder);
  $ok = $stmt->execute([
    ":usersid"      => $usersid,
    ":address"      => $addressid,      // NULL مسموح
    ":type"         => $orderstype,     // 1 أو 2
    ":pricedelivery"=> $pricedelivery,
    ":price"        => $ordersprice,
    ":coupon"       => $couponIdDb,     // NULL لو ما في كوبون
    ":total"        => $totalprice,
    ":paymethod"    => $paymentmethod,
    ":status"       => 0,               // جديد
    ":rating"       => null,
    ":noterating"   => null,
    ":delivery"     => null,            // 🔴 مهم: NULL لتفادي FK
    ":created"      => $nowTs,
    ":updated"      => $nowTs,
  ]);

  if (!$ok || $stmt->rowCount() <= 0) {
    $err = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(["status"=>"failure","message"=>"Order insert failed","error"=>$err[2]??null]); exit;
  }

  $orderId = (int)$con->lastInsertId();

  // ===== 4) ربط السلة بالطلب =====
  $stmt = $con->prepare("UPDATE cart SET cart_orders = :oid WHERE cart_usersid = :uid AND cart_orders = 0");
  $stmt->execute([":oid"=>$orderId, ":uid"=>$usersid]);

  // ===== 5) ردّ JSON =====
  echo json_encode([
    "status" => "success",
    "data" => [
      "order_id"     => $orderId,
      "total_price"  => $totalprice,
      "delivery"     => $pricedelivery,
      "coupon_id"    => $couponIdDb,        // null إذا ما في كوبون
      "discount_pct" => $coupondiscount,
      "delivery_id"  => null,               // لاحقاً لما تعمل Delivery
    ],
  ]);
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["status"=>"serverException","message"=>$e->getMessage()]); exit;
}
