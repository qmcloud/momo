<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace Appapi\Controller;
use Common\Controller\HomebaseController; 
/**
 * 支付回调
 */
class PayController extends HomebaseController {
	
	private $wxDate = null;
	//支付宝 回调
	public function notify_ali() {

		require_once(SITE_PATH."alipay/alipay_app/alipay.config.php");
		require_once(SITE_PATH."alipay/alipay_app/lib/alipay_core.function.php");
		require_once(SITE_PATH."alipay/alipay_app/lib/alipay_rsa.function.php");
		require_once(SITE_PATH."alipay/alipay_app/lib/alipay_notify.class.php");

		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		$this->logali("ali_data:".json_encode($_POST));
		if($verify_result) {//验证成功
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];
			
			//交易金额
			$total_fee = $_POST['total_fee'];
			
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $where['orderno']=$out_trade_no;
                $where['money']=$total_fee;
                $where['status']=0;
                $where['type']=1;
				$orderinfo=M("users_charge")->where($where)->find();	
				$this->logali("orderinfo:".json_encode($orderinfo));	
				if($orderinfo){
					/* 更新会员虚拟币 */
					$coin=$orderinfo['coin']+$orderinfo['coin_give'];
					M("users")->where("id='{$orderinfo['touid']}'")->setInc("coin",$coin);
					/* 更新 订单状态 */
					M("users_charge")->where("id='{$orderinfo['id']}'")->save(array("status"=>1,"trade_no"=>$trade_no));

					$this->logali("成功");	
					echo "success";		//请不要修改或删除
					exit;
				}else{
					$this->logali("orderno:".$out_trade_no.' 订单信息不存在');		
				}											
			}
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "fail";		//请不要修改或删除
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
			$this->logali("验证失败");		
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}			
		
	}
	/* 支付宝支付 */
	
	/* 微信支付 */		
	public function notify_wx(){
		$config=getConfigPri();

		//$xmlInfo = $GLOBALS['HTTP_RAW_POST_DATA'];

		$xmlInfo=file_get_contents("php://input"); 

		//解析xml
		$arrayInfo = $this -> xmlToArray($xmlInfo);
		$this -> wxDate = $arrayInfo;
		$this -> logwx("wx_data:".json_encode($arrayInfo));//log打印保存
		if($arrayInfo['return_code'] == "SUCCESS"){
			if($return_msg != null){
				echo $this -> returnInfo("FAIL","签名失败");
				$this -> logwx("签名失败:".$sign);//log打印保存
				exit;
			}else{
				$wxSign = $arrayInfo['sign'];
				unset($arrayInfo['sign']);
				$arrayInfo['appid']  =  $config['wx_appid'];
				$arrayInfo['mch_id'] =  $config['wx_mchid'];
				$key =  $config['wx_key'];
				ksort($arrayInfo);//按照字典排序参数数组
				$sign = $this -> sign($arrayInfo,$key);//生成签名
				$this -> logwx("数据打印测试签名signmy:".$sign.":::微信sign:".$wxSign);//log打印保存
				if($this -> checkSign($wxSign,$sign)){
					echo $this -> returnInfo("SUCCESS","OK");
					$this -> logwx("签名验证结果成功:".$sign);//log打印保存
					$this -> orderServer();//订单处理业务逻辑
					exit;
				}else{
					echo $this -> returnInfo("FAIL","签名失败");
					$this -> logwx("签名验证结果失败:本地加密：".$sign.'：：：：：三方加密'.$wxSign);//log打印保存
					exit;
				}
			}
		}else{
			echo $this -> returnInfo("FAIL","签名失败");
			$this -> logwx($arrayInfo['return_code']);//log打印保存
			exit;
		}			
	}
	
	private function returnInfo($type,$msg){
		if($type == "SUCCESS"){
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code></xml>";
		}else{
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";
		}
	}		
	
	//签名验证
	private function checkSign($sign1,$sign2){
		return trim($sign1) == trim($sign2);
	}
	/* 订单查询加值业务处理
	 * @param orderNum 订单号	   
	 */
	private function orderServer(){
		$info = $this -> wxDate;
		$this->logwx("info:".json_encode($info));
        $where['orderno']=$info['out_trade_no'];
        $where['status']=0;
        $where['type']=2;
		$orderinfo=M("users_charge")->where($where)->find();
		//$this->logwx("sql:".M()->getLastSql());
		$this->logwx("orderinfo:".json_encode($orderinfo));
		if($orderinfo){
			/* 更新会员虚拟币 */
			$coin=$orderinfo['coin']+$orderinfo['coin_give'];
			M("users")->where("id='{$orderinfo['touid']}'")->setInc("coin",$coin);
			/* 更新 订单状态 */
			M("users_charge")->where("id='{$orderinfo['id']}'")->save(array("status"=>1,"trade_no"=>$info['transaction_id']));
            $this->logwx("orderno:".$out_trade_no.' 支付成功');
		}else{
			$this->logwx("orderno:".$out_trade_no.' 订单信息不存在');		
			return false;
		}		

	}		
	/**
	* sign拼装获取
	*/
	private function sign($param,$key){
		
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
	
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
	/**
	* xml转为数组
	*/
	private function xmlToArray($xmlStr){
		$msg = array(); 
		$postStr = $xmlStr; 
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
		return $msg;
	}
	
	/* 微信支付 */

	/* 苹果支付 */
	
	public function notify_ios(){
		$content=file_get_contents("php://input");  
		$data = json_decode($content,true); 

        $this->logios("data:".json_encode($data));
        
		$receipt = $data["receipt-data"];     
		$isSandbox = $data["sandbox"];
		$out_trade_no = $data["out_trade_no"];
		$info = $this->getReceiptData($receipt, $isSandbox);   
		
		$this->logios("info:".json_encode($info));
		
		$iforderinfo=M("users_charge")->where(["trade_no"=>$info['transaction_id'],"type"=>'3'])->find();

		if($iforderinfo){
			echo '{"status":"fail","info":"非法提交-001"}';
            exit;
		}
        
        $chargeinfo=M("charge_rules")->where(["product_id"=>$info['product_id']])->find();
        if(!$chargeinfo){
            echo '{"status":"fail","info":"非法提交-002"}';
            exit;
        }

		//判断订单是否存在
        $where['orderno']=$out_trade_no;
        $where['coin']=$chargeinfo['coin'];
        $where['status']=0;
        $where['type']=3;
		$orderinfo=M("users_charge")->where($where)->find();
        
		if($orderinfo){
			/* 更新会员虚拟币 */
			$coin=$orderinfo['coin']+$orderinfo['coin_give'];
			M("users")->where("id='{$orderinfo['touid']}'")->setInc("coin",$coin);
			/* 更新 订单状态 */
			M("users_charge")->where("id='{$orderinfo['id']}'")->save(array("status"=>1,"trade_no"=>$info['transaction_id'],"ambient"=>$info['ambient']));
            $this->logios("orderno:".$out_trade_no.' 支付成功');
		}else{
			$this->logios("orderno:".$out_trade_no.' 订单信息不存在');
			echo '{"status":"fail","info":"订单信息不存在-003"}'; 		
			exit();
		}
		echo '{"status":"success","info":"充值支付成功"}';
		exit;
	}		
	public function getReceiptData($receipt, $isSandbox){ 
		$config=getConfigPri();
        
        $this->logios("isSandbox:".$isSandbox);
        $this->logios("isSandboxc:".$config['ios_sandbox']);
        $ambient=0;
		if ($isSandbox == $config['ios_sandbox']) {   
			//沙盒
			$endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
            $ambient=0;
		}else {  
			//生产
			$endpoint = 'https://buy.itunes.apple.com/verifyReceipt'; 
            $ambient=1;
		}   

		$postData = json_encode(   
				array('receipt-data' => $receipt)   
		);   

		$ch = curl_init($endpoint);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	//关闭安全验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  	//关闭安全验证
		curl_setopt($ch, CURLOPT_POST, true);   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);   

		$response = curl_exec($ch);   
		$errno    = curl_errno($ch);   
		$errmsg   = curl_error($ch);   
		curl_close($ch);   
        
        $this->logios("getReceiptData response:".json_encode($response));
        $this->logios("getReceiptData errno:".json_encode($errno));
        $this->logios("getReceiptData errmsg:".json_encode($errmsg));

		if($errno != 0) {   
			echo '{"status":"fail","info":"服务器出错，请联系管理员"}';
			exit;
		}   
		$data = json_decode($response,1);   

		if (!is_array($data)) {   
			echo '{"status":"fail","info":"验证失败,如有疑问请联系管理"}';
			exit;
		}   

		if (!isset($data['status']) || $data['status'] != 0) {   
			echo '{"status":"fail","info":"验证失败,如有疑问请联系管理"}';
			exit;
		}   

        $newdata=end($data['receipt']['in_app']);
		return array(     
			'product_id'     =>  $newdata['product_id'],   
			'transaction_id' =>  $newdata['transaction_id'],   
			'ambient' =>  $ambient,   
		);
	}   
		
	/* 苹果支付 */	
			
	/* 打印log */
	public function logali($msg){
		file_put_contents(SITE_PATH.'data/paylog/logali_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}		
	/* 打印log */
	public function logwx($msg){
		file_put_contents(SITE_PATH.'data/paylog/logwx_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}			
	/* 打印log */
	public function logios($msg){
		file_put_contents(SITE_PATH.'data/paylog/logios_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}						

}


