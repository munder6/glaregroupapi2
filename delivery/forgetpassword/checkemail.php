<?php

include "../../connect.php";

$email = filterRequest("email");
$verfiycode = rand(10000 , 99999);
$stmt = $con->prepare("SELECT * FROM delivery WHERE delivery_email = ?");
$stmt->execute(array($email));
$count = $stmt->rowCount();
result($count);


if($count > 0){
    $data = array("delivery_verifycode" => $verfiycode);
    updateData("delivery", $data , "delivery_email = '$email'", false);
    sendEmail($email, "Verfiy Code Glare Group", "Verfiy Code is $verfiycode");
}