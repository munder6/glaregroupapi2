<?php

include __DIR__ . "/../../connect.php";

$table = "delivery";

$requiredFields = ["username", "password", "email", "phone"];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        printFailure("Missing required field: $field");
        exit;
    }
}

$username = filterRequest("username");
$password = sha1($_POST["password"]);
$email    = filterRequest("email");
$phone    = filterRequest("phone");
$approve  = isset($_POST["approve"]) ? filterRequest("approve") : "1";
$verify   = isset($_POST["verifycode"]) ? filterRequest("verifycode") : rand(10000, 99999);

$stmt = $con->prepare("SELECT delivery_id FROM delivery WHERE delivery_email = ? OR delivery_phone = ?");
$stmt->execute([$email, $phone]);

if ($stmt->rowCount() > 0) {
    printFailure("Phone Or Email");
    exit;
}

$data = [
    "delivery_name"      => $username,
    "delivery_password"  => $password,
    "delivery_email"     => $email,
    "delivery_phone"     => $phone,
    "delivery_verifycode"=> $verify,
    "delivery_approve"   => $approve,
];

insertData($table, $data);