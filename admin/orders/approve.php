<?php

include "../../connect.php";

$ordersid = filterRequest("ordersid");
$usersid = filterRequest("usersid");

$data = array(
    "orders_status" => 1
);

updateData("orders" , $data , "orders_id = $ordersid AND orders_status = 0" );


// sendGCM("Success" , "The order has been approved" , "users$usersid" , "none" , "refreshorderpending");

// insertNotify("Success", "The order has been approved", $usersid, "users$usersid", "none", "refreshorderpending");