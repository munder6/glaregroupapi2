<?php



include "connect.php";

$alldata = array();


$alldata['status'] = "success";

 $setting = getAllData("setting", "1 = 1", null, false);

 $alldata['setting'] = $setting;

////////////////////
// Get Categories //
////////////////////

$categories = getAllData("categories", null, null, false);

$alldata['categories'] = $categories;


///////////////
// Get Items //
///////////////



$items = getAllData("itemstopselling", "1 = 1", null, false);

$alldata['itemstopselling'] = $items;


$allitems = getAllData("itemsview", "1 = 1", null, false);
$alldata['itemsview'] = $allitems;



echo json_encode($alldata);

?>