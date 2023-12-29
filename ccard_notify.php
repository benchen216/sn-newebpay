<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once('config/conf.php');
require_once('mwt-newebpay_sdk.php');
$TradeInfo = file_get_contents("php://input");

$arr = mb_split("&",$TradeInfo);
$get_aes = str_replace("TradeInfo=","",$arr[3]);

$data = create_aes_decrypt($get_aes,$hashKey,$hashIV);
$json = json_decode($data);

if($json->Status == "SUCCESS"){
    //繳費完成時.....
    $myfile = fopen(time().".txt", "w") or die("Unable to open file!");
    fwrite($myfile,$data);
    fclose($myfile);
    include_once "config/db_config.php";
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
    if ($conn->connect_error) {
        die("連線失敗: " . $conn->connect_error);
    }
    $conn->query("SET NAMES 'utf8mb4'");
    $sql = "SELECT * FROM `sn` WHERE `status` IS NULL LIMIT 1";
    $result2 = $conn->query($sql);
    if ($result2->num_rows > 0) {
        $sn = $result2->fetch_assoc();
        $sql = "UPDATE `trade` SET `status` = 'paid' WHERE `trade`.`MerchantOrderNo` = '".$json->Result->MerchantOrderNo."'";
        $result = $conn->query($sql);
        $sql = "UPDATE `sn` SET `status` = 'used',`MerchantOrderNo` = '".$json->Result->MerchantOrderNo."' WHERE `sn`.`id` = ".$sn['id'];
        $result = $conn->query($sql);
        $sql = "SELECT * FROM `trade` WHERE `MerchantOrderNo` = '".$json->Result->MerchantOrderNo."'";
        $result3 = $conn->query($sql);
        $ref = $result3->fetch_assoc();
        include_once "config/smtp_config.php";
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';
        require 'PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    // 設置SMTP伺服器
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // 可選的，用於調試
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST; // 您的SMTP伺服器地址
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER; // 您的SMTP帳號
    $mail->Password   = SMTP_PASS; // 您的SMTP密碼
    $mail->SMTPSecure = 'tls';                      // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587; // SMTP伺服器的端口號
    $mail->CharSet = 'UTF-8';

    // 設置寄件人和收件人
    $mail->setFrom(MAIL_FROM, '自動序號發送機器人');
    $mail->addAddress($ref['email'], $ref['username']); // 收件人

    // 郵件主題和內容
    $mail->isHTML(true);
    $mail->Subject = '行銷助手序號';
    $mail->Body    = '您的序號為：'.$sn['sn'];

    $mail->send();
    echo '郵件已成功發送！';
     $myfile = fopen(time()."ms.txt", "w") or die("Unable to open file!");
        fwrite($myfile,$data."no sn");
        fclose($myfile);
} catch (Exception $e) {
    $myfile = fopen(time()."me.txt", "w") or die("Unable to open file!");
        fwrite($myfile,$mail->ErrorInfo.$ref['email']."\n".$ref['username']."\n".$sn['sn']);
        fclose($myfile);
    echo "郵件發送失敗： {$mail->ErrorInfo}";
}
}else{
        $sql = "UPDATE `trade` SET `status` = 'paid-error' WHERE `trade`.`MerchantOrderNo` = '".$json->Result->MerchantOrderNo."'";
        $result = $conn->query($sql);
        $myfile = fopen(time().".txt", "w") or die("Unable to open file!");
        fwrite($myfile,$data."no sn");
        fclose($myfile);
    die("no sn");
}
}
?>