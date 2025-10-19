<?php

include "../../connect.php";
$table = "admin";


$username = filterRequest("username");
$password = sha1($_POST["password"]);
$email = filterRequest("email");
$phone = filterRequest("phone");
$theme = isset($_POST["theme"]) ? filterRequest("theme") : "light";
$language = isset($_POST["language"]) ? filterRequest("language") : "ar";
$verfiycode = rand(10000 , 99999);

$stmt = $con->prepare("SELECT * FROM admin WHERE admin_email = ? OR admin_phone = ? ");
$stmt->execute(array($email, $phone));
$count = $stmt->rowCount();
if ($count >0 ){
    printFailure("Phone Or Email");
}else{
    $data = array(
        "admin_name" => $username,
        "admin_password" => $password,
        "admin_email" => $email,
        "admin_phone" => $phone,
        "admin_theme" => $theme,
        "admin_language" => $language,
        "admin_verifycode" => $verfiycode,
    );
    sendEmail($email, "Verfiy Code Glare Group E-commerce App", "Hello $username \n Your Verfiy Code is $verfiycode \n Please don't share it to Anyone !");
    insertData($table, $data);
}