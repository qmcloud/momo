<?php
/**
 * 登录、注册
 */
session_start();
class Api_Login extends PhalApi_Api { 
	public function getRules() {
        return array(
			'userLogin' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
				'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
				'pushid' => array('name' => 'pushid', 'type' => 'string', 'desc' => '极光ID'),
				'country_code' => array('name' => 'country_code', 'type' => 'string', 'desc' => '国际代码'),
				//'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
            ),
			'userReg' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
				'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
                'source' => array('name' => 'source', 'type' => 'string',  'default'=>'pc', 'desc' => '来源设备'),
				'country_code' => array('name' => 'country_code', 'type' => 'string', 'desc' => '国际代码'),
            ),
			'userFindPass' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
				'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
				'country_code' => array('name' => 'country_code', 'type' => 'string', 'desc' => '国际代码'),
            ),	
			'userLoginByThird' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '第三方openid'),
                'type' => array('name' => 'type', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '第三方标识'),
                'nicename' => array('name' => 'nicename', 'type' => 'string',   'default'=>'',  'desc' => '第三方昵称'),
                'avatar' => array('name' => 'avatar', 'type' => 'string',  'default'=>'', 'desc' => '第三方头像'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
                'source' => array('name' => 'source', 'type' => 'string',  'default'=>'pc', 'desc' => '来源设备'),
                'pushid' => array('name' => 'pushid', 'type' => 'string', 'desc' => '极光ID'),
            ),
			
			'getCode' => array(
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
				'country_code' => array('name' => 'country_code', 'type' => 'string', 'desc' => '国际代码'),
			),
			
			'getForgetCode' => array(
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
				'country_code' => array('name' => 'country_code', 'type' => 'string', 'desc' => '国际代码'),
			),
            'getUnionid' => array(
				'code' => array('name' => 'code', 'type' => 'string','desc' => '微信code'),
			),

            'logout' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
			),
        );
	}
	
    /**
     * 会员登陆 需要密码
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userLogin() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$user_login=checkNull($this->user_login);
		$user_pass=checkNull($this->user_pass);
		$pushid=checkNull($this->pushid);
		$country_code=checkNull($this->country_code);

        $domain = new Domain_Login();
        $info = $domain->userLogin($user_login,$user_pass,$country_code);

		if($info==1001){
			$rs['code'] = 1001;
            $rs['msg'] = '账号或密码错误';
            return $rs;	
		}else if($info==1002){
			$rs['code'] = 1002;
            $rs['msg'] = '该账号已被禁用';
            return $rs;	
		}
	
        $rs['info'][0] = $info;
        
        if($pushid){
             $domain->upUserPush($info['id'],$pushid);
        }
				
		
        return $rs;
    }		
   /**
     * 会员注册
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userReg() {

        $rs = array('code' => 0, 'msg' => '注册成功', 'info' => array());
	
		$user_login=checkNull($this->user_login);
		$user_pass=checkNull($this->user_pass);
		$user_pass2=checkNull($this->user_pass2);
		$source=checkNull($this->source);
		$code=checkNull($this->code);
		$country_code=checkNull($this->country_code);
        
        if(!$_SESSION['reg_mobile'] || !$_SESSION['reg_mobile_code']){
            $rs['code'] = 1001;
            $rs['msg'] = '请先获取验证码';
            return $rs;		
        }
	
		if($user_login!=$_SESSION['reg_mobile']){
            $rs['code'] = 1001;
            $rs['msg'] = '手机号码不一致';
            return $rs;					
		}

		if($code!=$_SESSION['reg_mobile_code']){
            $rs['code'] = 1002;
            $rs['msg'] = '验证码错误';
            return $rs;					
		}	

		if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;					
		}	
        
		$check = passcheck($user_pass);

		if($check==0){
            $rs['code'] = 1004;
            $rs['msg'] = '密码6-12位数字与字母';
            return $rs;										
        }else if($check==2){
            $rs['code'] = 1005;
            $rs['msg'] = '密码不能纯数字或纯字母';
            return $rs;										
        }			
		$domain = new Domain_Login();
		$info = $domain->userReg($user_login,$user_pass,$source,$country_code);

		if($info==1006){
			$rs['code'] = 1006;
            $rs['msg'] = '该手机号已被注册！';
            return $rs;	
		}else if($info==1007){
			$rs['code'] = 1007;
            $rs['msg'] = '注册失败，请重试';
            return $rs;	
		}

        //$rs['info'][0] = $info;
		
		$_SESSION['reg_mobile'] = '';
		$_SESSION['reg_mobile_code'] = '';
		$_SESSION['reg_mobile_expiretime'] = '';
			
        return $rs;
    }		
	/**
     * 会员找回密码
     * @desc 用于会员找回密码
     * @return int code 操作码，0表示成功，1表示验证码错误，2表示用户密码不一致,3短信手机和登录手机不一致 4、用户不存在 801 密码6-12位数字与字母
     * @return array info 
     * @return string msg 提示信息
     */
    public function userFindPass() {
		
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$user_login=checkNull($this->user_login);
		$user_pass=checkNull($this->user_pass);
		$user_pass2=checkNull($this->user_pass2);
		$code=checkNull($this->code);
		$country_code=checkNull($this->country_code);
		
		if(!$_SESSION['forget_mobile'] || !$_SESSION['forget_mobile_code']){
            $rs['code'] = 1001;
            $rs['msg'] = '请先获取验证码';
            return $rs;		
        }
        
	 	if($user_login!=$_SESSION['forget_mobile']){
            $rs['code'] = 1001;
            $rs['msg'] = '手机号码不一致';
            return $rs;					
		}

		if($code!=$_SESSION['forget_mobile_code']){
            $rs['code'] = 1002;
            $rs['msg'] = '验证码错误';
            return $rs;					
		}	
		

		if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;					
		}	

		$check = passcheck($user_pass);
		if($check== 0 ){
            $rs['code'] = 1004;
            $rs['msg'] = '密码6-12位数字与字母';
            return $rs;										
        }else if($check== 2){
            $rs['code'] = 1005;
            $rs['msg'] = '密码不能纯数字或纯字母';
            return $rs;										
        }		

		$domain = new Domain_Login();
        $info = $domain->userFindPass($user_login,$user_pass,$country_code);	
		
		if($info==1006){
			$rs['code'] = 1006;
            $rs['msg'] = '该帐号不存在';
            return $rs;	
		}else if($info===false){
			$rs['code'] = 1007;
            $rs['msg'] = '重置失败，请重试';
            return $rs;	
		}
		
		$_SESSION['forget_mobile'] = '';
		$_SESSION['forget_mobile_code'] = '';
		$_SESSION['forget_mobile_expiretime'] = '';

        return $rs;
    }
	
    /**
     * 第三方登录
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userLoginByThird() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$openid=checkNull($this->openid);
		$type=checkNull($this->type);
		$nicename=checkNull($this->nicename);
		$avatar=checkNull($this->avatar);
		$source=checkNull($this->source);
		$sign=checkNull($this->sign);
		$pushid=checkNull($this->pushid);
        
        
        $checkdata=array(
            'openid'=>$openid
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']='签名错误';
			return $rs;	
        }
        
        
        
        $domain = new Domain_Login();
        $info = $domain->userLoginByThird($openid,$type,$nicename,$avatar,$source);
		
        if($info==1001){
            $rs['code'] = 1001;
            $rs['msg'] = '该账号已被禁用';
            return $rs;					
		}

        $rs['info'][0] = $info;
        
        if($pushid){
            $domain->upUserPush($info['id'],$pushid);
        }

        return $rs;
    }
	
	/**
	 * 获取注册短信验证码
	 * @desc 用于注册获取短信验证码
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string msg 提示信息
	 */
	 
	public function getCode() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$mobile = checkNull($this->mobile);
		$sign = checkNull($this->sign);
		$country_code=checkNull($this->country_code);
		
		$ismobile=checkMobile($mobile);
		if(!$ismobile){
			$rs['code']=1001;
			$rs['msg']='请输入正确的手机号';
			return $rs;	
		}
        
        $checkdata=array(
            'mobile'=>$mobile
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']='签名错误';
			return $rs;	
        }
		
        $where="user_login='{$mobile}'";
        
		$checkuser = checkUser($where);	
        
        if($checkuser){
            $rs['code']=1004;
			$rs['msg']='该手机号已注册，请登录';
			return $rs;
        }

		if($_SESSION['reg_mobile']==$mobile && $_SESSION['reg_mobile_expiretime']> time() ){
			$rs['code']=1002;
			$rs['msg']='验证码5分钟有效，请勿多次发送';
			return $rs;
		}
		
        $limit = ip_limit();	
		if( $limit == 1){
			$rs['code']=1003;
			$rs['msg']='您已当日发送次数过多';
			return $rs;
		}		
		$mobile_code = random(6,1);
		
		/* 发送验证码 */
 		$result=sendCode($mobile,$mobile_code,$country_code);
		if($result['code']==0){
			$_SESSION['reg_mobile'] = $mobile;
			$_SESSION['reg_mobile_code'] = $mobile_code;
			$_SESSION['reg_mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['reg_mobile'] = $mobile;
            $_SESSION['reg_mobile_code'] = $result['msg'];
            $_SESSION['reg_mobile_expiretime'] = time() +60*5;
            
            $rs['code']=1002;
			$rs['msg']='验证码为：'.$result['msg'];
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		} 
		
		
		return $rs;
	}		

	/**
	 * 获取找回密码短信验证码
	 * @desc 用于找回密码获取短信验证码
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string msg 提示信息
	 */
	 
	public function getForgetCode() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$mobile = checkNull($this->mobile);
		$sign = checkNull($this->sign);
		$country_code = checkNull($this->country_code);
		
		$ismobile=checkMobile($mobile);
		if(!$ismobile){
			$rs['code']=1001;
			$rs['msg']='请输入正确的手机号';
			return $rs;	
		}
        
        $checkdata=array(
            'mobile'=>$mobile
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']='签名错误';
			return $rs;	
        }
        
        $where="user_login='{$mobile}'";
        $checkuser = checkUser($where);	
        
        if(!$checkuser){
            $rs['code']=1004;
			$rs['msg']='该手机号未注册';
			return $rs;
        }

		if($_SESSION['forget_mobile']==$mobile && $_SESSION['forget_mobile_expiretime']> time() ){
			$rs['code']=1002;
			$rs['msg']='验证码5分钟有效，请勿多次发送';
			return $rs;
		}

        $limit = ip_limit();	
		if( $limit == 1){
			$rs['code']=1003;
			$rs['msg']='您已当日发送次数过多';
			return $rs;
		}	
		$mobile_code = random(6,1);
		
		/* 发送验证码 */
 		$result=sendCode($mobile,$mobile_code,$country_code);
		if($result['code']==0){
			$_SESSION['forget_mobile'] = $mobile;
			$_SESSION['forget_mobile_code'] = $mobile_code;
			$_SESSION['forget_mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['forget_mobile'] = $mobile;
            $_SESSION['forget_mobile_code'] = $result['msg'];
            $_SESSION['forget_mobile_expiretime'] = time() +60*5;
            
            $rs['code']=1002;
			$rs['msg']='验证码为：'.$result['msg'];
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		} 
		
		return $rs;
	}	
    
	/**
	 * 获取微信登录unionid
	 * @desc 用于获取微信登录unionid
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string info[0].unionid 微信unionid
	 * @return string msg 提示信息
	 */    
    public function getUnionid(){
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $code=checkNull($this->code);
        
        if($code==''){
            $rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
            
        }

        //$configpri=getConfigPri();
    
        //$AppID = $configpri['login_wx_appid'];
        //$AppSecret = $configpri['login_wx_appsecret'];
        $AppID = 'wxbee8d98b9852d612';
        $AppSecret = 'f9d4f74d9412691eeb271dc7632f24b6';
        /* 获取token */
        //$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
        $url="https://api.weixin.qq.com/sns/jscode2session?appid={$AppID}&secret={$AppSecret}&js_code={$code}&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
        //file_put_contents('./getUnionid.txt',date('Y-m-d H:i:s').' 提交参数信息 code:'.json_encode($code)."\r\n",FILE_APPEND);
        //file_put_contents('./getUnionid.txt',date('Y-m-d H:i:s').' 提交参数信息 arr:'.json_encode($arr)."\r\n",FILE_APPEND);
        if($arr['errcode']){
            $rs['code']=1003;
			$rs['msg']='配置错误';
            //file_put_contents('./getUnionid.txt',date('Y-m-d H:i:s').' 提交参数信息 arr:'.json_encode($arr)."\r\n",FILE_APPEND);
			return $rs;
        }
        
        

        /* 小程序 绑定到 开放平台 才有 unionid  否则 用 openid  */
        $unionid=$arr['unionid'];

        if(!$unionid){
            //$rs['code']=1002;
			//$rs['msg']='公众号未绑定到开放平台';
			//return $rs;
            
            $unionid=$arr['openid'];
        }
        
        $rs['info'][0]['unionid'] = $unionid;
        return $rs;
    }
    
	/**
	 * 退出
	 * @desc 用于用户退出 注销极光
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function logout() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = $this->uid;
		$token=checkNull($this->token);
        
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        

		$info = userLogout($uid);


		return $rs;			
	}

}
