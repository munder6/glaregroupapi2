<?php

include "connect.php";

$userid = filterRequest("id");


getAllData("notification", "notification_usersid = $userid");