<?php

include "../../connect.php";

$table = "coupon";

if (!isset($_POST["coupon_id"])) {
    printFailure("Missing required field: coupon_id");
    exit;
}

$couponId = filterRequest("coupon_id");

deleteData($table, "coupon_id = $couponId");