<?php
require_once('config/conf.php');
require_once('mwt-newebpay_sdk.php');

/* 金鑰與版本設定 */
$MerchantID = $merchantID;
$HashKey = $hashKey;
$HashIV = $hashIV;
$URL = $url;
$VER = $ver;

/* 送給藍新資料 */
$trade_info_arr = array(
	'MerchantID' => $merchantID,
	'RespondType' => 'JSON',
	'TimeStamp' => time(),
	'Version' => $VER,
	'MerchantOrderNo' => getOrderNo(),
	'Amt' => $NTD,
	'ItemDesc' => $Order_Title,
	'CREDIT' => 1,
	'VACC' => 0,//ATM
	'ReturnURL' => $ReturnURL, //支付完成 返回商店網址
	'NotifyURL' => $NotifyURL_ccard, //支付通知網址
	'CustomerURL' =>$CustomerURL, //商店取號網址
	'ClientBackURL' => $ClientBackURL //支付取消 返回商店網址
);

if (isset($_GET['pay']) == 1 && $_GET['pay'] == "y"){
	$TradeInfo = create_mpg_aes_encrypt($trade_info_arr, $HashKey, $HashIV);
	$SHA256 = strtoupper(hash("sha256", SHA256($HashKey,$TradeInfo,$HashIV)));
	echo CheckOut($URL,$MerchantID,$TradeInfo,$SHA256,$VER);
}
if (isset($_POST['pay']) == 1 && $_POST['pay'] == "y" && isset($_POST['username'])== 1 && isset($_POST['phone'])==1 && isset($_POST['email'])==1 ){
    $myfile = fopen(time().'x'.".txt", "w") or die("Unable to open file!");
            fwrite($myfile,$_POST['username']."\n".$_POST['phone']."\n".$_POST['email']."\n");
            fclose($myfile);
    include_once "config/db_config.php";
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
    if ($conn->connect_error) {
        die("連線失敗: " . $conn->connect_error);
    }
    $conn->query("SET NAMES 'utf8mb4'");
	$TradeInfo = create_mpg_aes_encrypt($trade_info_arr, $HashKey, $HashIV);
	$SHA256 = strtoupper(hash("sha256", SHA256($HashKey,$TradeInfo,$HashIV)));
    $sql = "INSERT INTO `trade` (`username`, `phone`, `email`, `status`, `MerchantOrderNo`) VALUES ('".$_POST['username']."', '".$_POST['phone']."', '".$_POST['email']."', '"."unpaid"."', '".$trade_info_arr['MerchantOrderNo']."')";
    $result = $conn->query($sql);
	echo CheckOut($URL,$MerchantID,$TradeInfo,$SHA256,$VER);
}

?>