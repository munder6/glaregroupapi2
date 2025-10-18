<?php

include "../../connect.php";

$table = "coupon";

if (!isset($_POST["coupon_id"]) || !isset($_POST["coupon_active"])) {
    printFailure("Missing required fields");
    exit;
}

$couponId = filterRequest("coupon_id");
$couponActive = filterRequest("coupon_active");

$data = array(
    "coupon_active" => $couponActive,
);

updateData($table, $data, "coupon_id = $couponId");