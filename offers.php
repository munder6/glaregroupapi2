<?php

include "./connect.php";



$stmt = $con->prepare("SELECT itemsview.*, 1 AS favorite, (items_price - (items_price * items_descount / 100)) as itemspricedescount FROM itemsview
INNER JOIN favorite on favorite.favorite_itemsid = itemsview.items_id 
WHERE items_descount != 0
UNION ALL 
SELECT * , 0 as favorite , (items_price - (items_price * items_descount / 100)) as itemspricedescount FROM itemsview
WHERE items_descount != 0 AND items_id NOT IN ( SELECT itemsview.items_id FROM itemsview
INNER JOIN favorite ON favorite.favorite_itemsid = itemsview.items_id )");


$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count  = $stmt->rowCount();

if($count > 0){
    echo json_encode(array("status" => "success", "data" => $data));
}else {
    echo json_encode(array("status" => "failure"));
}
