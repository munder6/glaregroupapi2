

<?php

include "../connect.php";

$userid = filterRequest("id");

$data = getAllData("users", "users_id = '$userid' ", null, false);

// افترض أن $data هو مصفوفة من المستخدمين

echo json_encode(array(
    "status" => "success",
    "datausers" => $data
));
