<?php

include '../connect.php';

$couponName = filterRequest("couponName");
$now = date("Y-m-d H:i:s") ;

getData(
    "coupon",
    "coupon_name = '$couponName' AND coupon_expdate > '$now' AND coupon_count > 0 AND coupon_active = '1'"
);