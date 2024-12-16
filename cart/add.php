<?php

include '../connect.php';

$userid = filterRequest("userid");
$itemsid = filterRequest("itemsid");


$count = getData("cart", "cart_itemid = $itemsid AND cart_usersid = $userid", null ,null, false);


    $data = array(
        "cart_usersid" => $userid,
        "cart_itemid" => $itemsid
    ) ;
    insertData("cart", $data);
