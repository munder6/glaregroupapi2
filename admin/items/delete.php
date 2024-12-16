<?php

include "../../connect.php";

$id = filterRequest("id"); 
$imagename = filterRequest("imagename"); 




deleteData("items", "items_id = $id");

deleteFile("../../upload/items", $imagename);