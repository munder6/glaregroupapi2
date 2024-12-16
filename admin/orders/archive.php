<?php

include "../../connect.php";

$userid = filterRequest("id");


getAllData("ordersview" , "1 = 1 AND orders_status = 4");