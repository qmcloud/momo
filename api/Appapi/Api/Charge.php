<?php
/**
 * 充值
 */
class Api_Charge extends PhalApi_Api {

	public function getRules() {
		return array(
			'getAliOrder' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'changeid' => array('name' => 'changeid', 'type' => 'int',  'require' => true, 'desc' => '充值规则ID'),
				'coin' => array('name' => 'coin', 'type' => 'string',  'require' => true, 'desc' => '钻石'),
				'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '充值金额'),
			),
			'getWxOrder' => array( 
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'changeid' => array('name' => 'changeid', 'type' => 'string',  'require' => true, 'desc' => '充值规则ID'),
				'coin' => array('name' => 'coin', 'type' => 'string',  'require' => true, 'desc' => '钻石'),
				'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '充值金额'),
			),
			'getIosOrder' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'changeid' => array('name' => 'changeid', 'type' => 'string',  'require' => true, 'desc' => '充值规则ID'),
				'coin' => array('name' => 'coin', 'type' => 'string',  'require' => true, 'desc' => '钻石'),
				'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '充值金额'),
			),
		);
	}
	
	/* 获取订单号 */
	protected function getOrderid($uid){
		$orderid=$uid.'_'.date('YmdHis').rand(100,999);
		return $orderid;
	}

	/**
	 * 微信支付
	 * @desc 用于 微信支付 获取订单号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0] 支付信息
	 * @return string msg 提示信息
	 */
	public function getWxOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$changeid=$this->changeid;
		$coin=checkNull($this->coin);
		$money=checkNull($this->money);

		$orderid=$this->getOrderid($uid);
		$type=2;
		
		if($coin==0){
			$rs['code']=1002;
			$rs['msg']='信息错误';		
			return $rs;						
		}					
		
		$configpri = getConfigPri(); 
		$configpub = getConfigPub(); 

		 //配置参数检测
					
		if($configpri['wx_appid']== "" || $configpri['wx_mchid']== "" || $configpri['wx_key']== ""){
			$rs['code'] = 1002;
			$rs['msg'] = '微信未配置';
			return $rs;					 
		}
		
		$orderinfo=array(
			"uid"=>$uid,
			"touid"=>$uid,
			"money"=>$money,
			"coin"=>$coin,
			"orderno"=>$orderid,
			"type"=>$type,
			"status"=>0,
			"addtime"=>time()
		);

		
		$domain = new Domain_Charge();
		$info = $domain->getOrderId($changeid,$orderinfo);
		if($info==1003){
			$rs['code']=1003;
			$rs['msg']='订单信息有误，请重新提交';
            return $rs;	
		}else if(!$info){
			$rs['code']=1001;
			$rs['msg']='订单生成失败';
            return $rs;	
		}

			 
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
		$time = time();
			
		$paramarr = array(
			"appid"       =>   $configpri['wx_appid'],
			"body"        =>    "充值{$coin}虚拟币",
			"mch_id"      =>    $configpri['wx_mchid'],
			"nonce_str"   =>    $noceStr,
			"notify_url"  =>    $configpub['site'].'/Appapi/pay/notify_wx',
			"out_trade_no"=>    $orderid,
			"total_fee"   =>    $money*100, 
			"trade_type"  =>    "APP"
		);
		$sign = $this -> sign($paramarr,$configpri['wx_key']);//生成签名
		$paramarr['sign'] = $sign;
		$paramXml = "<xml>";
		foreach($paramarr as $k => $v){
			$paramXml .= "<" . $k . ">" . $v . "</" . $k . ">";
		}
		$paramXml .= "</xml>";
			 
		$ch = curl_init ();
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
		@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在  
		@curl_setopt($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_POST, 1);
		@curl_setopt($ch, CURLOPT_POSTFIELDS, $paramXml);
		@$resultXmlStr = curl_exec($ch);
		if(curl_errno($ch)){
			//print curl_error($ch);
			file_put_contents('./wxpay.txt',date('y-m-d H:i:s').' 提交参数信息 ch:'.json_encode(curl_error($ch))."\r\n",FILE_APPEND);
		}
		curl_close($ch);

		$result2 = $this->xmlToArray($resultXmlStr);
        
        if($result2['return_code']=='FAIL'){
            $rs['code']=1005;
			$rs['msg']=$result2['return_msg'];
            return $rs;	
        }
		$time2 = time();
		$prepayid = $result2['prepay_id'];
		$sign = "";
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
		$paramarr2 = array(
			"appid"     =>  $configpri['wx_appid'],
			"noncestr"  =>  $noceStr,
			"package"   =>  "Sign=WXPay",
			"partnerid" =>  $configpri['wx_mchid'],
			"prepayid"  =>  $prepayid,
			"timestamp" =>  $time2
		);
		$paramarr2["sign"] = $this -> sign($paramarr2,$configpri['wx_key']);//生成签名
		
		$rs['info'][0]=$paramarr2;
		return $rs;			
	}		
	
	/**
	* sign拼装获取
	*/
	protected function sign($param,$key){
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
	protected function xmlToArray($xmlStr){
		$msg = array(); 
		$postStr = $xmlStr; 
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
		return $msg;
	}	
		
	/**
	 * 支付宝支付
	 * @desc 用于支付宝支付 获取订单号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].orderid 订单号
	 * @return string msg 提示信息
	 */
	public function getAliOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$changeid=$this->changeid;
		$coin=checkNull($this->coin);
		$money=checkNull($this->money);
		
		$orderid=$this->getOrderid($uid);
		$type=1;
		
		if($coin==0){
			$rs['code']=1002;
			$rs['msg']='信息错误';		
			return $rs;						
		}	
		
		$orderinfo=array(
			"uid"=>$uid,
			"touid"=>$uid,
			"money"=>$money,
			"coin"=>$coin,
			"orderno"=>$orderid,
			"type"=>$type,
			"status"=>0,
			"addtime"=>time()
		);
		
		$domain = new Domain_Charge();
		$info = $domain->getOrderId($changeid,$orderinfo);
		if($info==1003){
			$rs['code']=1003;
			$rs['msg']='订单信息有误，请重新提交';
		}else if(!$info){
			$rs['code']=1001;
			$rs['msg']='订单生成失败';
		}
		
		$rs['info'][0]['orderid']=$orderid;
		return $rs;
	}		

	/**
	 * 苹果支付
	 * @desc 用于苹果支付 获取订单号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].orderid 订单号
	 * @return string msg 提示信息
	 */
	public function getIosOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$changeid=$this->changeid;
		$coin=checkNull($this->coin);
		$money=checkNull($this->money);
		
		$orderid=$this->getOrderid($uid);
		$type=3;
		
		if($coin==0){
			$rs['code']=1002;
			$rs['msg']='信息错误';		
			return $rs;						
		}

		$configpri = getConfigPri(); 
		
		$orderinfo=array(
			"uid"=>$uid,
			"touid"=>$uid,
			"money"=>$money,
			"coin"=>$coin,
			"orderno"=>$orderid,
			"type"=>$type,
			"status"=>0,
			"addtime"=>time(),
			"ambient"=>$configpri['ios_sandbox']
		);
		
		$domain = new Domain_Charge();
		$info = $domain->getOrderId($changeid,$orderinfo);
		if($info==1003){
			$rs['code']=1003;
			$rs['msg']='订单信息有误，请重新提交';
		}else if(!$info){
			$rs['code']=1001;
			$rs['msg']='订单生成失败';
		}

		$rs['info'][0]['orderid']=$orderid;
		return $rs;
	}		
	


}
