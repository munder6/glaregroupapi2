<?php

include "../../connect.php";

$email = filterRequest("email");
$table = "admin";
$verfiy = filterRequest("verifycode");


$stmt = $con->prepare("SELECT * FROM $table WHERE admin_email = '$email' AND admin_verifycode = '$verfiy'");

$stmt->execute();

$count = $stmt->rowCount();

result($count);

?>