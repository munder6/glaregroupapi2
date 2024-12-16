<?php


include "../../connect.php";


$table = "items";


$name = filterRequest("name");
$namear = filterRequest("namear");
$desc = filterRequest("desc");
$descar = filterRequest("descar");
$imagename = filterRequest("imagename");
$count = filterRequest("count");
$price = filterRequest("price");
$descount = filterRequest("descount");
$catid = filterRequest("catid");

$datenow = filterRequest("datenow");

$imagename = imageUpload("../../upload/items" ,"files");


$data = array(
"items_name"     => $name,
"items_name_ar"  => $namear,
"items_desc"     => $desc,
"items_desc_ar"  => $descar,
"items_image"    => $imagename,
"items_count"    => $count,
"items_active"   => "1",
"items_price"    => $price,
"items_descount" => $descount,
"items_cat"      => $catid,
"items_date"     => $datenow
);


insertData($table , $data);

