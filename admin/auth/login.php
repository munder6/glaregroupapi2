<?php

include "../../connect.php";

$email = filterRequest("email");
$password = sha1($_POST["password"]);


getData("admin", "admin_email = ? AND admin_password = ?", array($email, $password));