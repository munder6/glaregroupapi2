<?php


include "../../connect.php";


$table = "items";


$imageold = filterRequest("imageold");
$id = filterRequest("id");
$name = filterRequest("name");
$namear = filterRequest("namear");
$desc = filterRequest("desc");
$descar = filterRequest("descar");
$imagename = filterRequest("imagename");
$count = filterRequest("count");
 $active = filterRequest("active");
$price = filterRequest("price");
$descount = filterRequest("descount");
$catid = filterRequest("catid");

$res = imageUpload("../../upload/items" ,"files");

if($res == 'empty'){
    $data = array(
        "items_name"     => $name,
        "items_name_ar"  => $namear,
        "items_desc"     => $desc,
        "items_desc_ar"  => $descar,
        "items_count"    => $count,
        "items_active"   => $active,
        "items_price"    => $price,
        "items_descount" => $descount,
        "items_cat"      => $catid,
        );
}else{
    deleteFile("../../upload/items" , $imageold);
    $data = array(
        "items_name"     => $name,
        "items_name_ar"  => $namear,
        "items_desc"     => $desc,
        "items_desc_ar"  => $descar,
        "items_image"    => $res,
        "items_count"    => $count,
        "items_active"   => $active,
        "items_price"    => $price,
        "items_descount" => $descount,
        "items_cat"      => $catid,
        );
}





updateData($table , $data, "items_id = $id");