<?php
include "../../connect.php";

$table = "items";

$name      = filterRequest("name");
$namear    = filterRequest("namear");
$desc      = filterRequest("desc");
$descar    = filterRequest("descar");
$count     = filterRequest("count");
$price     = filterRequest("price");
$descount  = filterRequest("descount");
$catid     = filterRequest("catid");
$datenow   = filterRequest("datenow");

$img = imageUpload(__DIR__ . "/../../upload/items", "files");

if ($img === 'empty' || $img === 'fail') {
  echo json_encode([
    "status"  => "failure",
    "message" => "image upload failed or missing"
  ]);
  exit;
}

$data = array(
  "items_name"     => $name,
  "items_name_ar"  => $namear,
  "items_desc"     => $desc,
  "items_desc_ar"  => $descar,
  "items_image"    => $img,      
  "items_count"    => $count,
  "items_active"   => "1",
  "items_price"    => $price,
  "items_descount" => $descount,
  "items_cat"      => $catid,
  "items_date"     => $datenow
);

insertData($table , $data);
