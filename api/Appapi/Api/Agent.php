<?php
/**
 * 分销
 */
class Api_Agent extends PhalApi_Api {

	public function getRules() {
		return array(
            'getCode' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),
		);
	}
	

	/**
	 * 分享信息
	 * @desc 用于 获取分享信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].code 邀请码
	 * @return string info[0].href 二维码链接
	 * @return string info[0].qr 二维码图片链接
	 * @return string msg 提示信息
	 */
	public function getCode() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Agent();
		$info = $domain->getCode($uid);
        
        if(!$info){
            $rs['code']=1001;
			$rs['msg']='信息错误';
			return $rs;
        }

        //http://livenewtest.yunbaozb.com/index.php?g=Portal&m=index&a=scanqr

		$href=get_upload_path('/index.php?g=Portal&m=index&a=scanqr');
		$info['href']=$href;
        $qr=scerweima($href);
        $info['qr']=get_upload_path($qr);
        
		$rs['info'][0]=$info;
		return $rs;			
	}		
	

}
