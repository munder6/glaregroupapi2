<?php

include "../../connect.php";

$ordersid = filterRequest("ordersid");
$usersid = filterRequest("usersid");

$data = array(
    "orders_status" => 4
);

updateData("orders" , $data , "orders_id = $ordersid AND orders_status = 3" );


// sendGCM("Success" , "The order has been approved" , "users$usersid" , "none" , "refreshorderpending");

// insertNotify("Alert", "Youe order has been delivered", $usersid, "users$usersid", "none", "refreshorderpending");

// sendGCM("Alert", "The Orders has been delivered to the client", "services" , "" ,  "" );