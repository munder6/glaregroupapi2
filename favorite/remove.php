<?php
include '../connect.php';

$userid = filterRequest("userid");
$itemsid = filterRequest("itemsid");

deleteData("favorite", "favorite_usersid = $userid AND favorite_itemsid = $itemsid");

