<?php
/**
 * 守护
 */
class Api_Guard extends PhalApi_Api {

	public function getRules() {
		return array(
			'getGuardList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户TOKEN'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
			),
            
            'getList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户TOKEN'),
			),
            
            'buyGuard' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户TOKEN'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string','default' => '', 'desc' => '直播流名'),
				'guardid' => array('name' => 'guardid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '守护ID'),
			),
		);
	}

	/**
	 * 获取守护用户列表
	 * @desc 用于 获取守护用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id  用户ID
	 * @return string info[].type  守护类型
	 * @return string info[].contribute  周贡献
	 * @return string msg 提示信息
	 */
	public function getGuardList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
        $liveuid=$this->liveuid;
		
		$data=array(
			"liveuid"=>$liveuid,
		);

		$domain = new Domain_Guard();
		$info = $domain->getGuardList($data);
        

		
		$rs['info']=$info;
		return $rs;			
	}	


	/**
	 * 获取守护列表
	 * @desc 用于 获取守护列表价格信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin  用户余额
	 * @return array info[0].privilege  特权列表
	 * @return string info[0].privilege[].title  标题
	 * @return string info[0].privilege[].des  描述
	 * @return string info[0].privilege[].thumb_c  彩图
	 * @return string info[0].privilege[].thumb_g  灰图
	 * @return array info[0].list  守护列表
	 * @return string info[0].list[].id  守护ID
	 * @return string info[0].list[].name  守护名称
	 * @return string info[0].list[].type  守护类型
	 * @return string info[0].list[].coin  价格
	 * @return array info[0].list[].privilege  所有特权
	 * @return string msg 提示信息
	 */
	public function getList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        
        $uid=$this->uid;
		$token=checkNull($this->token);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        

        $key='guard_list';
        $list=getcaches($key);
        if(!$list){
            $domain = new Domain_Guard();
            $list = $domain->getList();
            
            setcaches($key,$list);
        }
        
        foreach($list as $k=>$v){
            if($v['type']==2){
                $list[$k]['privilege']=array('0','1','2','3');
            }else{
                $list[$k]['privilege']=array('0','1');
            }
        }
        
        $privilege=array(
            array('title'=>'身份标识','des'=>'聊天区显示守护身份标识','thumb_c'=>get_upload_path('/public/guard/guard_1.png'),'thumb_g'=>get_upload_path('/public/guard/guard_0.png')),
            array('title'=>'进场特效','des'=>'拥有进场金光以及专属欢迎语','thumb_c'=>get_upload_path('/public/guard/enter_c.png'),'thumb_g'=>get_upload_path('/public/guard/enter_g.png')),
            array('title'=>'专属礼物','des'=>'拥有直播间守护用户才可以送出的专属礼物','thumb_c'=>get_upload_path('/public/guard/gift_c.png'),'thumb_g'=>get_upload_path('/public/guard/gift_g.png')),
            array('title'=>'尊贵特权','des'=>'防止除主播外的其他人踢出禁言','thumb_c'=>get_upload_path('/public/guard/privilege_c.png'),'thumb_g'=>get_upload_path('/public/guard/privilege_g.png')),
        );
		
		$rs['info'][0]['privilege']=$privilege;
		$rs['info'][0]['list']=$list;
        
        $domain2 = new Domain_User();
		$coin=$domain2->getBalance($uid);
        $rs['info'][0]['coin']=$coin['coin'];
        
		return $rs;			
	}		
	

	/**
	 * 购买守护
	 * @desc 用于 用户购买守护
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].coin 用户余额
	 * @return string info[0].level 用户等级
	 * @return string info[0].total 主播增加映票
	 * @return string info[0].votestotal 主播总映票
	 * @return string info[0].guard_nums 守护人数
	 * @return string info[0].type 守护类型
	 * @return string info[0].endtime 到期时间戳
	 * @return string info[0].endtime_date 格式化到期日期
	 * @return string msg 提示信息
	 */
	public function buyGuard() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=$this->uid;
		$token=checkNull($this->token);
        $liveuid=$this->liveuid;
        $stream=checkNull($this->stream);
        $guardid=$this->guardid;
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $data=array(
            'uid'=>$uid,
            'liveuid'=>$liveuid,
            'stream'=>$stream,
            'guardid'=>$guardid,
        );
        

        $domain = new Domain_Guard();
        $result = $domain->buyGuard($data);
        
        $rs=$result;

		return $rs;			
	}		
}
