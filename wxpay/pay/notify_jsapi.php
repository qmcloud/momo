<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			 Log::DEBUG("已进入");
			$attach=$result['attach'];
			$out_trade_no=$result['out_trade_no'];
			$fee=$result['total_fee'];
			$transaction_id=$result['transaction_id'];

			$total_fee=$fee*0.01;
			if(function_exists('mysqli_close')){
				$link = new mysqli("127.0.0.1","livenew","1a9f8wJPtRdf","livenew");
				if($link){
					$link->query("set names utf8");
					$result = $link->query("select * from cmf_users_charge where orderno='{$out_trade_no}' and status='0' and type='2'");
					Log::DEBUG("select * from cmf_users_charge where orderno='{$out_trade_no}' and status='0' and type='2'");
					$row = $result->fetch_assoc();
					if($row){
							$coin=$row['coin']+$row['coin_give'];
							$link->query("update cmf_users set coin=coin+{$coin}  where id='$row[touid]'");
							$link->query("update cmf_users_charge set status='1',trade_no='{$transaction_id}' where id={$row['id']}");						
							Log::DEBUG("支付成功");							
					}else{
							Log::DEBUG($out_trade_no.' 订单信息不存在');
					}
				}else{
					Log::DEBUG("数据库链接失败");
				} 					
			}else{
				$link = mysql_connect("127.0.0.1","livenew","1a9f8wJPtRdf");
				if($link){
					mysql_select_db("livenew",$link);
					mysql_query("set names utf8");
					$result = mysql_query("select * from cmf_users_charge where orderno='{$out_trade_no}' and status='0' and type='2'");
					Log::DEBUG("select * from cmf_users_charge where orderno='{$out_trade_no}' and status='0' and type='2'");
					$row = mysql_fetch_assoc($result);
					$str = json_encode($row);
					if($row){
							$coin=$row['coin']+$row['coin_give'];
							mysql_query("update cmf_users set coin=coin+{$coin}  where id='$row[touid]'");
							mysql_query("update cmf_users_charge set status='1',trade_no='{$transaction_id}' where id={$row['id']}");						
							Log::DEBUG("支付成功");							
					}else{
							Log::DEBUG($out_trade_no.' 订单信息不存在');
					}
				}else{
					Log::DEBUG("数据库链接失败");
				} 					
				
			}
			
			
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
