<?php
/**
 * 用户连麦
 */
class Api_Linkmic extends PhalApi_Api {

	public function getRules() {
		return array(
            'setMic' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户Token'),
				'ismic' => array('name' => 'ismic', 'type' => 'int', 'require' => true, 'desc' => '连麦开关，0关1开'),
			),
            
            'isMic' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
			),
            
			'RequestLVBAddrForLinkMic' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
			),
			'RequestPlayUrlWithSignForLinkMic' => array( 
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'originStreamUrl' => array('name' => 'originStreamUrl', 'type' => 'string',  'require' => true, 'desc' => '流地址'),
			),
			'MergeVideoStream' => array( 
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'mergeparams' => array('name' => 'mergeparams', 'type' => 'string',  'require' => true, 'desc' => '混流参数'),
			),
		);
	}
    
	/**
	 * 设置连麦开关
	 * @desc 用于 用户设置当前直播的连麦开关
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].pushurl 推流地址
	 * @return string info[].timestamp 当前时间
	 * @return string info[].playurl 播流地址
	 * @return string msg 提示信息
	 */
	public function setMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=$this->uid;        
        $token=checkNull($this->token);
        $ismic=checkNull($this->ismic);

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$result = $domain->setMic($uid,$ismic);


		$rs['msg']='设置成功';
		return $rs;			
	}		

	/**
	 * 判断主播是否开启连麦
	 * @desc 用于 判断主播是否开启连麦
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function isMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

      
        $uid=checkNull($this->uid);
        $liveuid=checkNull($this->liveuid);
        
        $configpri=getConfigPri();
        $mic_limit=$configpri['mic_limit'];
        
        $userinfo=getUserinfo($uid);
        
        if($mic_limit && $userinfo['level']<$mic_limit){
            $rs['code'] = 1002;
			$rs['msg'] = "用户等级达到{$mic_limit}级才可与主播连麦哦~";
			return $rs;
        }
        
        $domain = new Domain_Linkmic();
		$result = $domain->isMic($liveuid);

        if(!$result){
            $rs['code'] = 1001;
			$rs['msg'] = '主播未开启连麦功能哦~';
			return $rs;
        }

		return $rs;	
	}
	
	/**
	 * 获取连麦推拉流地址
	 * @desc 用于 获取连麦推拉流地址
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].pushurl 推流地址
	 * @return string info[].timestamp 当前时间
	 * @return string info[].playurl 播流地址
	 * @return string msg 提示信息
	 */
	public function RequestLVBAddrForLinkMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=$this->uid;
		$configpri = getConfigPri(); 
        $nowtime=time();
        $stream=$uid.'_'.$nowtime;
        $live_sdk=$configpri['live_sdk'];  //live_sdk  0表示金山SDK 1表示腾讯SDK
        if($live_sdk==1){
            $bizid = $configpri['tx_bizid'];
            $push_url_key = $configpri['tx_push_key'];
            $push = $configpri['tx_push'];
            $pull = $configpri['tx_pull'];

            $now_time2 = $nowtime + 3*60*60;
            $txTime = dechex($now_time2);
            
            $live_code = $stream ;

            $txSecret = md5($push_url_key . $live_code . $txTime);

            $safe_url = "&txSecret=" . $txSecret."&txTime=" .$txTime;

            $push_url = "rtmp://" . $push . "/live/" .  $live_code . "?bizid=" . $bizid .$safe_url;
            $play_url = "rtmp://" . $pull . "/live/" .$live_code. "?bizid=" . $bizid .$safe_url;
            
        }else if($configpri['cdn_switch']==5)
		{
			$wyinfo=PrivateKeyA('rtmp',$stream,1);
			$play_url=$wyinfo['ret']["rtmpPullUrl"];
			$wy_cid=$wyinfo['ret']["cid"];
			$push_url=$wyinfo['ret']["pushUrl"];
		}else{
			$push_url=PrivateKeyA('rtmp',$stream,1);
			$play_url=PrivateKeyA('rtmp',$stream,0);
		}
		
        $info=array(
			"pushurl" => $push_url,
			"timestamp" => $nowtime, 
			"playurl" => $play_url
		);

		$rs['info'][0]=$info;
		return $rs;			
	}		

	/**
	 * 获取鉴权流地址
	 * @desc 用于鉴权流地址
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].streamUrlWithSignature 鉴权地址
	 * @return string info[].timestamp 当前时间
	 * @return string msg 提示信息
	 */
	public function RequestPlayUrlWithSignForLinkMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$originalUrl=checkNull($this->originStreamUrl);
		
		$configpri = getConfigPri(); 

		$bizid = $configpri['tx_bizid'];
		$push_url_key = $configpri['tx_push_key'];
		
		$list1 = preg_split ('/\?/', $originalUrl);
        $originalUrl=$list1[0];
        
        $list = preg_split ('/\//', $originalUrl);
        $url = preg_split ('/\./', end($list));
		
        $now_time = time();
		$now_time = $now_time + 3*60*60;
		$txTime = dechex($now_time);
		
		$txSecret = md5($push_url_key . $url[0] . $txTime);
		
        $safe_url = $originalUrl."?txSecret=" . $txSecret ."&txTime=" .$txTime ."&bizid=".$bizid;

        $safe_url=str_replace(".flv",'',$safe_url);
        $safe_url=str_replace("http://",'rtmp://',$safe_url);

        $info=array(
			"streamUrlWithSignature" => $safe_url,
			"timestamp" => $now_time, 
		);


		$rs['info'][0]=$info;
		return $rs;			
	}		
	
	/**
	 * 连麦混流
	 * @desc 用于连麦混流
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function MergeVideoStream() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$mergeparams=html_entity_decode($this->mergeparams);
		
		$configpri = getConfigPri(); 

		$appid = $configpri['tx_appid'];
		$bizid = $configpri['tx_bizid'];
		$push_url_key = $configpri['tx_push_key'];
		$call_back_key = $configpri['tx_api_key'];
		

		$t=time()+60;
		$sign=$md5_val = md5($call_back_key . strval($t));;

		$param=$mergeparams;

		$url='http://fcgi.video.qcloud.com/common_access?appid='.$appid.'&interface=Mix_StreamV2&t='.$t.'&sign='.$sign;

		$ch = curl_init ();
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
		@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在  
		@curl_setopt($ch, CURLOPT_URL, $url);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_POST, 1);
		@curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		@curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length: ' . strlen($param)
			)
		);

		@$result = curl_exec($ch);
		if(curl_errno($ch)){
			//print curl_error($ch);
			file_put_contents('./MergeVideoStream.txt',date('y-m-d H:i:s').' 提交参数信息 ch:'.json_encode(curl_error($ch))."\r\n",FILE_APPEND);
		}
		curl_close($ch);
        //file_put_contents('./MergeVideoStream.txt',date('y-m-d H:i:s').' 提交参数信息 param:'.json_encode($param)."\r\n",FILE_APPEND);
        //file_put_contents('./MergeVideoStream.txt',date('y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);

		if(!$result || $result['code']!=0){
			$rs['code']=1002;
			$rs['msg']=$result['message'];
			return $rs;
		}

		return $rs;			
	}	

}
