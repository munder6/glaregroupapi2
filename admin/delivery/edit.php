<?php

include __DIR__ . "/../../connect.php";

$table = "delivery";

if (!isset($_POST["id"])) {
    printFailure("Missing required field: id");
    exit;
}

$id = filterRequest("id");

$data = [];

if (isset($_POST["username"])) {
    $data["delivery_name"] = filterRequest("username");
}

if (isset($_POST["email"])) {
    $data["delivery_email"] = filterRequest("email");
}

if (isset($_POST["phone"])) {
    $data["delivery_phone"] = filterRequest("phone");
}

if (isset($_POST["approve"])) {
    $data["delivery_approve"] = filterRequest("approve");
}

if (isset($_POST["verifycode"])) {
    $data["delivery_verifycode"] = filterRequest("verifycode");
}

if (!empty($_POST["password"])) {
    $data["delivery_password"] = sha1($_POST["password"]);
}

if (empty($data)) {
    printFailure("No data provided to update");
    exit;
}

$email = $data["delivery_email"] ?? null;
$phone = $data["delivery_phone"] ?? null;

if ($email !== null || $phone !== null) {
    $conditions = [];
    $params = [];

    if ($email !== null) {
        $conditions[] = "delivery_email = ?";
        $params[] = $email;
    }

    if ($phone !== null) {
        $conditions[] = "delivery_phone = ?";
        $params[] = $phone;
    }

    $params[] = $id;

    $sql = "SELECT delivery_id FROM delivery WHERE (" . implode(" OR ", $conditions) . ") AND delivery_id != ?";
    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        printFailure("Phone Or Email");
        exit;
    }
}

updateData($table, $data, "delivery_id = $id");