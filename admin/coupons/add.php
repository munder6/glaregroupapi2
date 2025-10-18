<?php

include "../../connect.php";

$table = "coupon";

$requiredFields = array(
    "coupon_name",
    "coupon_discount",
    "coupon_count",
    "coupon_expdate",
);

$data = array();
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        printFailure("Missing required field: $field");
        exit;
    }
    $data[$field] = filterRequest($field);
}

$data["coupon_active"] = isset($_POST["coupon_active"]) ? filterRequest("coupon_active") : "1";

insertData($table, $data);
