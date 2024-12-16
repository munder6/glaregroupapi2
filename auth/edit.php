<?php

include "../connect.php";

$table = "users";

$id = filterRequest("id");
$name = filterRequest("name");
$email = filterRequest("email");
$phone = filterRequest("phone");

$data = array();

// Check if each field is not empty before adding it to the $data array
if (!empty($name)) {
    $data["users_name"] = $name;
}

if (!empty($email)) {
    $data["users_email"] = $email;
}

if (!empty($phone)) {
    $data["users_phone"] = $phone;
}

// Only update if there is data to update
if (!empty($data)) {
    updateData($table, $data, "users_id = $id");
}