<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//date_default_timezone_set("Asia/Jerusalem");

define("MB", 1048576);

function filterRequest($requestname)
{
  return  htmlspecialchars(strip_tags($_POST[$requestname]));
}

function getAllData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    if($where == null){
    $stmt = $con->prepare("SELECT  * FROM $table");
    }else{
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    }
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true ){
        if ($count > 0){
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count;
    }else{
     if($count > 0){
            return array("status" => "success", "data" => $data);
     }else{
       return array("status" => "failure");
     }
    }
}

function getData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }else{
        return $count;
    }
}

function insertData($table, $data, $json = true)
{
    global $con;
    foreach ($data as $field => $v)
        $ins[] = ':' . $field;
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

    $stmt = $con->prepare($sql);
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
    if ($count > 0) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "failure"));
    }
  }
    return $count;
}


function updateData($table, $data, $where, $json = true)
{
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` =  ? ";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    $stmt = $con->prepare($sql);
    $stmt->execute($vals);
    $count = $stmt->rowCount();
    if ($json == true) {
    if ($count > 0) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "failure"));
    }
    }
    return $count;
}

function deleteData($table, $where, $json = true)
{
    global $con;
    $stmt = $con->prepare("DELETE FROM $table WHERE $where");
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function imageUpload($dir, $imageRequest)
{
    // نتائج مبدئية
    global $msgError;
    $msgError = null;

    // تأكد من وجود الملف
    if (!isset($_FILES[$imageRequest])) {
        return 'empty';
    }

    $f = $_FILES[$imageRequest];

    // تأكد من عدم وجود أخطاء رفع من PHP
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        // رجّع سبب الفشل لمزيد من التشخيص
        return 'fail';
    }

    // أنشئ المجلد إن لم يكن موجودًا
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            // فشل إنشاء المجلد
            return 'fail';
        }
    }

    // حدود وصيغ مسموحة
    $allowExt = array("jpg","jpeg","png","gif","webp","svg","pdf","mp3");
    $maxBytes = 2 * 1048576; // 2MB

    $origName = $f['name'] ?? 'file';

    // تنظيف الاسم: تحويل الرموز الغريبة + إزالة المسافات/الرموز غير الآمنة
    $safe = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $origName);
    if ($safe === false) { $safe = $origName; }
    $safe = preg_replace('/[^\w\.\-]+/u', '_', $safe);
    $safe = preg_replace('/_+/', '_', $safe);
    $safe = trim($safe, '_');

    // الامتداد
    $ext = strtolower(pathinfo($safe, PATHINFO_EXTENSION));

    if ($safe === '' || $ext === '') {
        return 'fail';
    }

    if (!in_array($ext, $allowExt, true)) {
        return 'fail';
    }

    // حجم الملف
    $size = isset($f['size']) ? (int)$f['size'] : 0;
    if ($size <= 0 || $size > $maxBytes) {
        return 'fail';
    }

    // اسم نهائي مع بادئة عشوائية لتفادي التعارض
    $base = pathinfo($safe, PATHINFO_FILENAME);
    $final = rand(1000, 10000) . '_' . $base . '.' . $ext;
    $target = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $final;

    // تأكد أن الملف فعلاً مرفوع من PHP
    if (!is_uploaded_file($f['tmp_name'])) {
        return 'fail';
    }

    // انقل الملف وتحقق من النتيجة
    if (!move_uploaded_file($f['tmp_name'], $target)) {
        return 'fail';
    }

    // صلاحيات الملف (اختياري)
    @chmod($target, 0644);

    return $final;
}




function deleteFile($dir, $imagename)
{
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

function checkAuthenticate()
{
    if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {
        if ($_SERVER['PHP_AUTH_USER'] != "wael" ||  $_SERVER['PHP_AUTH_PW'] != "wael12345") {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }

    // End 

  
}


function printFailure($message = "none")
  {
    echo  json_encode(array("status" => "failure", "message" => $message));
  }

  function printSuccess($message = "none")
  {
    echo  json_encode(array("status" => "success", "message" => $message));
  }

  function result($count){
    if($count ){
        printSuccess();
    }else{
        printFailure();
    }
  }



   function sendEmail($to, $title, $body){
  
    $header = "From support@glaregroup.com" . "\n" . "CC : Munder Ghanem";
    mail($to, $title, $body, $header);
   } 


   function sendGCM($title, $message, $topic, $pageid, $pagename)
{


    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        "to" => '/topics/' . $topic,
        'priority' => 'high',
        'content_available' => true,

        'notification' => array(
            "body" =>  $message,
            "title" =>  $title,
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default"

        ),
        'data' => array(
            "pageid" => $pageid,
            "pagename" => $pagename
        )

    );


    $fields = json_encode($fields);
    $headers = array(
        'Authorization: key=' . "AAAAH19Iz8g:APA91bGZqEELwzHYNmU6VEME7_Eb1ulNvodAKLRUS-cpMqniVyLYjwGCR7C5UdopVhrbHYoUHZ9ctOgkjwlRR3gbrLhpAuKPErGEkMIjkxh7J0FgmqT5cpuQRg4Z2EgZPKUPUpoq9mmC",
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    return $result;
    curl_close($ch);
}

function insertNotify($title, $body, $userid, $topic, $pageid, $pagename){
    global $con;
    $stmt = $con->prepare("INSERT INTO `notification`(`notification_title`, `notification_body`, `notification_usersid`) VALUES (? , ? , ?)");
    $stmt->execute(array($title , $body, $userid));
    sendGCM($title , $body , $topic , $pageid , $pagename);
    $count = $stmt->rowCount();
    return $count;
}
   




 
