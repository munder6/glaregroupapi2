<?php

include "../../connect.php";

$table = "coupon";

if (!isset($_POST["coupon_id"])) {
    printFailure("Missing required field: coupon_id");
    exit;
}

$couponId = filterRequest("coupon_id");

$updatableFields = array(
    "coupon_name",
    "coupon_discount",
    "coupon_maxdiscount",
    "coupon_type",
    "coupon_status",
    "coupon_count",
    "coupon_expdate",
    "coupon_active",
);

$data = array();

foreach ($updatableFields as $field) {
    if (isset($_POST[$field])) {
        $data[$field] = filterRequest($field);
    }
}

if (empty($data)) {
    printFailure("No data provided to update");
    exit;
}

updateData($table, $data, "coupon_id = $couponId");