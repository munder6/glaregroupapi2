<?php

include "../../connect.php";

$table = "users";

$userid = filterRequest("id");


deleteData($table, "users_id = $userid");