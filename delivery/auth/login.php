<?php

include "../../connect.php";

$email = filterRequest("email");
$password = sha1($_POST["password"]);


getData("delivery", "delivery_email = ? AND delivery_password = ?", array($email, $password));