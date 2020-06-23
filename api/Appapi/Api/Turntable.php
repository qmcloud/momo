<?php
/**
 * 大转盘
 */
class Api_Turntable extends PhalApi_Api {

	public function getRules() {
		return array(
            'turn' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'id' => array('name' => 'id', 'type' => 'int', 'desc' => '价格配置ID'),
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
                'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'getWin' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页面'),
			),
		);
	}
	
	/**
	 * 转盘配置
	 * @desc 用于获取转盘配置
	 * @return int code 操作码，0表示成功
	 * @return array  info 
     * @return array  info[0].config  价格配置
     * @return string info[0].config[].id 
	 * @return string info[0].config[].times 次数
	 * @return string info[0].config[].coin 价格
	 * @return array  info[0].list  奖品列表
	 * @return string info[0].list[].id 
	 * @return string info[0].list[].type 类型，0无奖1钻石2礼物
	 * @return string info[0].list[].type_val 类型值，type=0再接再厉 type=1钻石数量
	 * @return string info[0].list[].thumb 图片 type=1 钻石图标 type=2 礼物图标
	 * @return string msg 提示信息
	 */
	public function getTurntable() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $domain = new Domain_Turntable();
		$con = $domain->getConfig();
		$list = $domain->getTurntable();
        
        $info['config']=$con;
        $info['list']=$list;
        $rs['info'][0]=$info;

		return $rs; 
	}
    
	/**
	 * 抽奖
	 * @desc 用于 用户抽奖
	 * @return int code 操作码，0表示成功
	 * @return array info  
	 * @return string info[0].coin  余额
	 * @return array info[0].list  中奖列表
	 * @return string info[0].list[].id 奖品ID
	 * @return string info[0].list[].type 类型，0无奖1钻石2礼物
	 * @return string info[0].list[].type_val 类型值，type=0再接再厉 type=1钻石数量
	 * @return string info[0].list[].name 名称
	 * @return string info[0].list[].thumb 图片 type=1 钻石图标 type=2 礼物图标
	 * @return string info[0].list[].nums 数量
	 * @return string msg 提示信息
	 */
	public function turn() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $id=checkNull($this->id);
        $liveuid=checkNull($this->liveuid);
        $stream=checkNull($this->stream);
        
        if($uid<1 || $token=='' || $id<0 || $liveuid<1 || $stream==''){
            $rs['code'] = 1000;
			$rs['msg'] = '信息错误';
			return $rs;
        }
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $domain = new Domain_Turntable();
		$res = $domain->turn($uid,$id,$liveuid,$stream);
        

		return $res;			
	}
    
	/**
	 * 中奖记录
	 * @desc 用于获取中奖记录
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].type 类型，0无奖1钻石2礼物
	 * @return string info[].type_val 类型值，type=0再接再厉 type=1钻石数量
	 * @return string info[].thumb 图片 type=1 钻石图标 type=2 礼物图标
	 * @return string info[].nums 数量
	 * @return string info[].addtime 时间
	 * @return string msg 提示信息
	 */
	public function getWin() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $p=checkNull($this->p);
        
        if($uid<0 || $token=='' ){
            $rs['code'] = 1000;
			$rs['msg'] = '信息错误';
			return $rs;
        }
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $domain = new Domain_Turntable();
		$list = $domain->getWin($uid,$p);
        
        $rs['info']=$list;

		return $rs; 
	}
	

}
