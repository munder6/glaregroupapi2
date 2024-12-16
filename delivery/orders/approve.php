<?php

include "../../connect.php";

$ordersid = filterRequest("ordersid");
$usersid = filterRequest("usersid");
$deliveryid = filterRequest("deliveryid");

$data = array(
    "orders_status" => 3,
    "orders_delivery" => $deliveryid
);

updateData("orders" , $data , "orders_id = $ordersid AND orders_status = 2" );


// sendGCM("Success" , "The order has been approved" , "users$usersid" , "none" , "refreshorderpending");

// insertNotify("Alert", "Your Order is on the way !", $usersid, "users$usersid", "none", "refreshorderpending");

// sendGCM("Alert", "The Orders has been approved by delivery", "services" , "" ,  "" );
// sendGCM("Alert", "The Orders has been approved by delivery $deliveryid ", "delivery" , "" ,  "" );