<?php

include __DIR__ . "/../../connect.php";

$table = "delivery";

if (!isset($_POST["id"])) {
    printFailure("Missing required field: id");
    exit;
}

$id = filterRequest("id");

deleteData($table, "delivery_id = $id");