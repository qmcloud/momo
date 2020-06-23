<?php
/**
 * 引导页
 */
class Api_Guide extends PhalApi_Api {

	public function getRules() {
		return array(
		);
	}
	

	/**
	 * 引导页
	 * @desc 用于 获取引导页信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].switch 开关，0关1开
	 * @return string info[0].type 类型，0图片1视频
	 * @return string info[0].time 图片时间
	 * @return array info[0].list
	 * @return string info[0].list[].thumb 图片、视频链接
	 * @return string info[0].list[].href 页面链接
	 * @return string msg 提示信息
	 */
	public function getGuide() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		
		$domain = new Domain_Guide();
		$info = $domain->getGuide();

		
		$rs['info'][0]=$info;
		return $rs;			
	}		
	

}
