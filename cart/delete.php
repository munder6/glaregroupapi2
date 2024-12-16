<?php

include "../connect.php";

$userid = filterRequest("userid");
$itemsid = filterRequest("itemsid");

deleteData("cart", "cart_id = ( SELECT cart_id FROM cart WHERE cart_usersid = $userid AND cart_itemid = $itemsid LIMIT 1)" );
