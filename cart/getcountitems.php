<?php



include "../connect.php";

$userid = filterRequest("userid");
$itemsid = filterRequest("itemsid");

$stmt = $con->prepare("SELECT COUNT(cart.cart_id) AS countitems FROM `cart` WHERE cart_usersid =$userid AND cart_itemid =$itemsid");
$stmt->execute();

$count = $stmt->rowCount();
$data = $stmt->fetchColumn();

if ($count > 0){
    echo json_encode(array("status" => "success", "data" => $data));
}else{
    echo json_encode(array("status" => "success", "data" => "0"));
}

?>