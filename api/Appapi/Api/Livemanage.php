<?php
/**
 * 房间管理
 */
class Api_Livemanage extends PhalApi_Api {

	public function getRules() {
		return array(
            'getManageList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),
            
            'cancelManage' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '要解除的用户ID'),
			),
            
            'getRoomList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),
            
            'getShutList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
			),

            'cancelShut' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '要解除的用户ID'),
			),
            
            'getKickList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
			),
            
            'cancelKick' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '要解除的用户ID'),
			),
            
		);
	}
	

	/**
	 * 我的管理员
	 * @desc 用于获取主播房间内的管理员列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].nums 管理员数量
	 * @return string info[0].total 总数
	 * @return array info[0].list
	 * @return string info[0].list[].uid 用户id
	 * @return string msg 提示信息
	 */
	public function getManageList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Livemanage();
		$info = $domain->getManageList($uid);

		
		$rs['info'][0]=$info;
		return $rs;			
	}
    
    
	/**
	 * 解除管理
	 * @desc 用于解除用户管理
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function cancelManage() {
		$rs = array('code' => 0, 'msg' => '解除成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $touid=checkNull($this->touid);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Livemanage();
		$list = $domain->cancelManage($uid,$touid);


		return $rs;			
	}


	/**
	 * 我的房间
	 * @desc 用于获取我是管理员的直播间
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].liveuid 主播ID
	 * @return string msg 提示信息
	 */
	public function getRoomList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Livemanage();
		$list = $domain->getRoomList($uid);

		
		$rs['info']=$list;
		return $rs;			
	}


	/**
	 * 禁言用户
	 * @desc 用于获取房间禁言用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].uid 用户id
	 * @return string msg 提示信息
	 */
	public function getShutList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $uidtype = isAdmin($uid,$liveuid);

		if($uidtype==30 ){
			$rs["code"]=1001;
			$rs["msg"]='您不是该直播间的管理员，无权操作';
			return $rs;									
		}
		
		$domain = new Domain_Livemanage();
		$list = $domain->getShutList($liveuid);

		
		$rs['info']=$list;
		return $rs;			
	}


	/**
	 * 解除禁言
	 * @desc 用于解除用户禁言
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function cancelShut() {
		$rs = array('code' => 0, 'msg' => '解除成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        $touid=checkNull($this->touid);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $uidtype = isAdmin($uid,$liveuid);

		if($uidtype==30 ){
			$rs["code"]=1001;
			$rs["msg"]='您不是该直播间的管理员，无权操作';
			return $rs;									
		}
		
		$domain = new Domain_Livemanage();
		$list = $domain->cancelShut($liveuid,$touid);


		return $rs;			
	}


	/**
	 * 踢出用户
	 * @desc 用于获取房间踢出用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].uid 用户id
	 * @return string msg 提示信息
	 */
	public function getKickList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $uidtype = isAdmin($uid,$liveuid);

		if($uidtype==30 ){
			$rs["code"]=1001;
			$rs["msg"]='您不是该直播间的管理员，无权操作';
			return $rs;									
		}

		$domain = new Domain_Livemanage();
		$list = $domain->getKickList($liveuid);

		
		$rs['info']=$list;
		return $rs;			
	}
	
	/**
	 * 解除踢出
	 * @desc 用于解除用户踢出
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function cancelKick() {
		$rs = array('code' => 0, 'msg' => '解除成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        $touid=checkNull($this->touid);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $uidtype = isAdmin($uid,$liveuid);

		if($uidtype==30 ){
			$rs["code"]=1001;
			$rs["msg"]='您不是该直播间的管理员，无权操作';
			return $rs;									
		}
		
		$domain = new Domain_Livemanage();
		$list = $domain->cancelKick($liveuid,$touid);
        
		return $rs;			
	}
}
