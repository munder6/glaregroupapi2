<?php

include "../../connect.php";

$table = "users";

$userid = filterRequest("id");
$active = filterRequest("active");

$data = array(
    "users_active" => $active,
);

updateData($table, $data, "users_id = $userid");