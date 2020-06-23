<?php
/**
 * 背包
 */
class Api_Backpack extends PhalApi_Api {

	public function getRules() {
		return array(
            'getBackpack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),
		);
	}
	

	/**
	 * 背包礼物
	 * @desc 用于 获取背包礼物
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].nums 数量
	 * @return string msg 提示信息
	 */
	public function getBackpack() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
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
        
        
		$domain = new Domain_Backpack();
		$info = $domain->getBackpack($uid);

		
		$rs['info']=$info;
		return $rs;			
	}		
	

}
