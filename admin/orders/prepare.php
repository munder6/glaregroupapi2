<?php

include "../../connect.php";

$ordersid = filterRequest("ordersid");
$usersid = filterRequest("usersid");
$type = filterRequest("ordertype");

if($type == "0"){
    $data = array(
        "orders_status" => 2
    );
}else{
    $data = array(
        "orders_status" => 4
    );
}



updateData("orders" , $data , "orders_id = $ordersid AND orders_status = 1" );


// sendGCM("Success" , "The order has been approved" , "users$usersid" , "none" , "refreshorderpending");

// insertNotify("Success", "The order has been approved", $usersid, "users$usersid", "none", "refreshorderpending");


sendGCM("Alert", "An Order has been preaperd", "delivery", "" , "" );

if($type == "0"){
    sendGCM("Alert", "An Order has been preaperd", "delivery", "" , "" );
}