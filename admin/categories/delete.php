<?php

include "../../connect.php";

$id = filterRequest("id"); 
$imagename = filterRequest("imagename"); 




deleteData("categories", "categories_id = $id");

deleteFile("../../upload/categories", $imagename);