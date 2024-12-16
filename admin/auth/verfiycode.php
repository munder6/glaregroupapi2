<?php

include "../../connect.php";

$table = "admin";

$email = filterRequest("email");

$verfiy = filterRequest("verifycode");


$stmt = $con->prepare("SELECT * FROM $table WHERE admin_email = '$email' AND admin_verifycode = '$verfiy'");

$stmt->execute();

$count = $stmt->rowCount();

if($count > 0){

    $data = array("admin_approve" => "1");
    updateData("admin", $data, "admin_email = '$email'");
}else{

    printFailure("Verifycode not Correct");
}

?>