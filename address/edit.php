<?php


include '../connect.php';

$table = "address";

$addressid = filterRequest("addressid");
$city = filterRequest("city");
$name = filterRequest("name");
$street = filterRequest("street");
$lat = filterRequest("lat");
$long = filterRequest("long");


$data = array(
"address_city" => $city,
"address_street" => $street,
"address_name" => $name,
"address_lat" => $lat,
"address_long" => $long,
);

updateData($table, $data, "address_id = $addressid");
