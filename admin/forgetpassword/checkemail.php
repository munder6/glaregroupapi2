<?php

include "../../connect.php";

$table = "admin";
$email = filterRequest("email");
$verfiycode = rand(10000 , 99999);
$stmt = $con->prepare("SELECT * FROM $table WHERE admin_email = ?");
$stmt->execute(array($email));
$count = $stmt->rowCount();
result($count);


if($count > 0){
    $data = array("admin_verifycode" => $verfiycode);
    updateData("admin", $data , "admin_email = '$email'", false);
    sendEmail($email, "Verfiy Code Glare Group", "Verfiy Code is $verfiycode");
}