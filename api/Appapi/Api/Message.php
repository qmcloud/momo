<?php
/**
 * 系统消息
 */
class Api_Message extends PhalApi_Api {

	public function getRules() {
		return array(
			'getList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string',  'require' => true, 'desc' => '用户Token'),
				'p' => array('name' => 'p', 'type' => 'int','default'=>1, 'desc' => '页码'),
			),

		);
	}
	
	/**
	 * 系统消息
	 * @desc 用于 获取系统消息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0] 支付信息
	 * @return string msg 提示信息
	 */
	public function getList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$p=checkNull($this->p);
        
        if($p<1){
			$p=1;
		}
        
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Message();
		$list = $domain->getList($uid,$p);
		
        foreach($list as $k=>$v){
            $v['addtime']=date('Y-m-d H:i',$v['addtime']);
            $list[$k]=$v;
        }

		
		$rs['info']=$list;
		return $rs;			
	}		
	
}
