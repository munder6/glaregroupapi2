<?php

include "../../connect.php";
$table = "delivery";


$username = filterRequest("username");
$password = sha1($_POST["password"]);
$email = filterRequest("email");
$phone = filterRequest("phone");
$verfiycode = rand(10000 , 99999);

$stmt = $con->prepare("SELECT * FROM delivery WHERE delivery_email = ? OR delivery_phone = ? ");
$stmt->execute(array($email, $phone));
$count = $stmt->rowCount();
if ($count >0 ){
    printFailure("Phone Or Email");
}else{
    $data = array(
        "delivery_name" => $username,
        "delivery_password" => $password,
        "delivery_email" => $email,
        "delivery_phone" => $phone,
        "delivery_verfiycode" => $verfiycode,
    );
    sendEmail($email, "Verfiy Code Glare Group E-commerce App", "Hello $username \n Your Verfiy Code is $verfiycode \n Please don't share it to Anyone !");
    insertData($table, $data);
}