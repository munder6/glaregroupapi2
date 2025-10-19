<?php
include "../../connect.php";

$table = "items";

// اجلب الحقول من الريكوست
$name      = filterRequest("name");
$namear    = filterRequest("namear");
$desc      = filterRequest("desc");
$descar    = filterRequest("descar");
$count     = filterRequest("count");
$price     = filterRequest("price");
$descount  = filterRequest("descount");
$catid     = filterRequest("catid");
$datenow   = filterRequest("datenow");

// ارفع الصورة لمسار مطلق (زي ملف التعديل)
$img = imageUpload(__DIR__ . "/../../upload/items", "files");

// لو ما وصل ملف أو فشل النقل -> رجّع فشل واضح
if ($img === 'empty' || $img === 'fail') {
  echo json_encode([
    "status"  => "failure",
    "message" => "image upload failed or missing"
  ]);
  exit;
}

// جهّز البيانات للإدخال
$data = array(
  "items_name"     => $name,
  "items_name_ar"  => $namear,
  "items_desc"     => $desc,
  "items_desc_ar"  => $descar,
  "items_image"    => $img,        // ← الاسم الراجع من imageUpload
  "items_count"    => $count,
  "items_active"   => "1",
  "items_price"    => $price,
  "items_descount" => $descount,
  "items_cat"      => $catid,
  "items_date"     => $datenow
);

// نفّذ الإدخال
insertData($table , $data);
