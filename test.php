<?php
$dsn = "mysql:host=localhost;dbname=u268686400_shoppi";
$user = "u268686400_shoppi";
$pass = "Mmss123**";

try {
    $con = new PDO($dsn, $user, $pass);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>