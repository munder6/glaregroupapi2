<?php

include "../../connect.php";

$verfiycode = rand(10000 , 99999);
$email = filterRequest("email");
$data = array(
    "admin_verifycode" => $verfiycode
);

updateData("admin", $data , "admin_email = '$email'");




sendEmail($email, "Verfiy Code Glare Group E-commerce App", "Hello $username \n Your Verfiy Code is $verfiycode \n Please don't share it to Anyone !");

