<?php
$merchantID = ""; 									//商店代號
$hashKey    = ""; 	//HashKey
$hashIV     = ""; 						//HashIV
$url        = "https://ccore.newebpay.com/MPG/mpg_gateway"; //測試環境URL
$ver        = "1.5";

$ReturnURL       = "http://example.com/"; 			//支付完成 返回商店網址
$NotifyURL_atm   = "http://example.com/atm_notify.php"; 		//支付通知網址
$NotifyURL_ccard = "http://example.com/ccard_notify.php"; 	//支付通知網址
$ClientBackURL   = "http://example.com/"; 									//支付取消 返回商店網址
$CustomerURL    = "http://example.com/"; 									//商店取號網址
$NTD = 7980;											//商品價格
$Order_Title = "好玩的東西";		//商品名稱
$ATM_ExpireDate = 3;						//ATM付款到期日
?>