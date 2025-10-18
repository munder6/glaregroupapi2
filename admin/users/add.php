<?php

include "../../connect.php";

$table = "users";

$username = filterRequest("username");
$password = sha1($_POST["password"]);
$email = filterRequest("email");
$phone = filterRequest("phone");
$active = isset($_POST["active"]) ? filterRequest("active") : "1";

$verfiycode = rand(10000, 99999);

$stmt = $con->prepare("SELECT * FROM users WHERE users_email = ? OR users_phone = ? ");
$stmt->execute(array($email, $phone));
$count = $stmt->rowCount();

if ($count > 0) {
    printFailure("Phone Or Email");
    exit;
}

$data = array(
    "users_name" => $username,
    "users_password" => $password,
    "users_email" => $email,
    "users_phone" => $phone,
    "users_verifycode" => $verfiycode,
    "users_approve" => "1",
    "users_active" => $active,
);

insertData($table, $data);