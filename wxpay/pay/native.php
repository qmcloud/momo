<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'log.php';

//获取金额
$money = (int)$_GET['money'];
$coin = (int)$_GET['coin'];
//订单名称
$order_name = "充值秀场虚拟币".$coin;

$notify = new NativePay();

$orderid = WxPayConfig::MCHID.date("YmdHis");
echo $orderid;
$input = new WxPayUnifiedOrder();
$input->SetBody($order_name);
$input->SetAttach($order_name);
$input->SetOut_trade_no($orderid);
$input->SetTotal_fee($money);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");

$input->SetTrade_type("NATIVE");
$input->SetProduct_id("123456789");
$result = $notify->GetPayUrl($input);

$url2 = $result["code_url"];
?>

<html>

<body>

</body>

</html>