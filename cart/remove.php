<?php

include '../connect.php';

$userid = filterRequest("userid");
$itemsid = filterRequest("itemsid");

deleteData('cart',"CREATE TEMPORARY TABLE temp_cart AS
SELECT * FROM cart WHERE cart_usersid = $userid AND cart_itemid = $itemsid LIMIT 1 ;

DELETE FROM cart
WHERE cart_id IN (SELECT cart_id FROM temp_cart)");



