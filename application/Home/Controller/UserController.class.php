<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\HomebaseController; 
/**
 * 会员相关
 */
class UserController extends HomebaseController {
    
    protected $fields='id,user_nicename,avatar,avatar_thumb,sex,signature,coin,consumption,votestotal,province,city,birthday,user_status,login_type,last_login_time';
	
    //首页
	public function index() {

    }	
	/* 手机验证码 */
	public function getCode(){
		
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
        
		$verify = new \Think\Verify();
		$checkverify=$verify->check(I('captcha'), "");	
		if(!$checkverify){
            $rs['errno']=1120;
            $rs['errmsg']='圖型驗證碼錯誤';
            echo json_encode($rs);
			exit;
		}
        $mobile = I("mobile");
		$countrycode = I("countrycode");//国家代码
		$type = I("type");

		if($type=='reg'){
			$where['user_login']=$mobile;
			
			$checkuser = checkUser($where);	
			
			if($checkuser){
				$rs['errno']=1006;
				$rs['errmsg']='該手機已註冊，請登入';
				echo json_encode($rs);
				exit;
			}
		}

		if($_SESSION['mobile']==$mobile && $_SESSION['mobile_expiretime']> time() ){
            $rs['errno']=1007;
            $rs['errmsg']='驗證碼5分鐘有效，請勿多發';
            echo json_encode($rs);
			exit;
		}
        
        
		$limit = ip_limit();	
		if( $limit == 1){
            $rs['errno']=1003;
            $rs['errmsg']='您當日發送次數過多';
            echo json_encode($rs);
			exit;
		}	

		$mobile_code = random(6,1);

		//密码可以使用明文密码或使用32位MD5加密
		$result = sendCode($mobile,$mobile_code,$countrycode); 
		if($result['code']===0){
			$_SESSION['mobile'] = $mobile;
			$_SESSION['mobile_code'] = $mobile_code;
			$_SESSION['mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['mobile'] = $mobile;
            $_SESSION['mobile_code'] = $result['msg'];
            $_SESSION['mobile_expiretime'] = time() +60*5;
            
            $rs['errno']=1120;
            $rs['errmsg']='驗證碼為：'.$result['msg'];
            echo json_encode($rs);
			exit;
		}else{
            $rs['errno']=1004;
            $rs['errmsg']=$result['msg'];
            echo json_encode($rs);
			exit;
		} 
        $rs['errmsg']='驗證碼已發送';
        echo json_encode($rs);
        exit;
	}			
	/* 图片验证码 */
	public function getCaptcha(){
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
            $rs['data']['captcha']='./index.php?g=api&m=checkcode&a=index&length=4&font_size=14&width=100&height=34&charset=2345678&use_noise=1&use_curve=0';
            $rs['errmsg']='請求成功';
            echo json_encode($rs);
			exit;
	}		
		
	/* 登录 */
/* 	$user_login!=$_SESSION['mobile'] */
	public function userLogin(){
        
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
        
		$user_login=I("mobile");
		$pass=I("pass");
		$countrycode=I("countrycode");//国家代码
		
		$user_pass=setPass($pass);
		
		$User=M("users");
		
        $where['user_login']=$user_login;
		$userinfo=$User->where($where)->where("user_type='2'")->find();
		
		if(!$userinfo || $userinfo['user_pass'] != $user_pass || $userinfo['country_code'] != $countrycode){
            $rs['errno']=1120;
            $rs['errmsg']='帳號或密碼錯誤';
            echo json_encode($rs);
			exit;						
		}else if($userinfo['user_status']==0){
            $rs['errno']=1120;
            $rs['errmsg']='帳號已被禁用';
            echo json_encode($rs);
			exit;	
		}
		$userinfo['level']=getLevel($userinfo['experience']);
        

        $token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
        $userinfo['token']=$token;

        
        $this->updateToken($userinfo['id'],$userinfo['token']);

		session('uid',$userinfo['id']);
		session('token',$userinfo['token']);
		session('user',$userinfo);
		
        $rs['userid']=$userinfo['id'];
        $rs['errmsg']='登入成功';
        echo json_encode($rs);
        exit;

	} 	
		
	/* 注册 */
	public function userReg(){
        
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
        
		$user_login=I("mobile");
		$pass=I("pass");
		$code=I("code");
		$countrycode=I("countrycode");//国家代码
		
		if($user_login!=$_SESSION['mobile']){	
            $rs['errno']=1120;
            $rs['errmsg']='手機號碼不一致';
            echo json_encode($rs);
			exit;					
		}

		if($code!=$_SESSION['mobile_code']){
            $rs['errno']=1120;
            $rs['errmsg']='驗證碼錯誤';
            echo json_encode($rs);
			exit;				
			
		}	
		$check = passcheck($pass);

		if($check==0){
            $rs['errno']=1120;
            $rs['errmsg']='密碼6-12位數字與字母';
            echo json_encode($rs);
			exit;	
		}else if($check==2){
            $rs['errno']=1120;
            $rs['errmsg']='密码不能纯數字或纯字母';
            echo json_encode($rs);
			exit;		
		}	

		$user_pass=setPass($pass);
		
		$User=M("users");
		$where['user_login']=$user_login;
		$where['countrycode']=$countrycode;
		$ifreg=$User->field("id")->where($where)->find();
		if($ifreg){
            $rs['errno']=1120;
            $rs['errmsg']='該手機號碼已被註冊';
            echo json_encode($rs);
			exit;	
		}
		
		/* 无信息 进行注册 */
		$configPri=getConfigPri();
        $reg_reward=$configPri['reg_reward'];
		$data=array(
				'user_login' => $user_login,
				'user_email' => '',
				'mobile' =>$user_login,
				'user_nicename' =>'WEB用户'.substr($user_login,-4),
				'user_pass' =>$user_pass,
				'signature' =>'None',
				'avatar' =>'/default.jpg',
				'avatar_thumb' =>'/default_thumb.jpg',
				'last_login_ip' =>get_client_ip(0,true),
				'create_time' => date("Y-m-d H:i:s"),
				'last_login_time' => date("Y-m-d H:i:s"),
				'user_status' => 1,
				"user_type"=>2,//会员
				"country_code"=>$countrycode,//国家代码
		);

		if($reg_reward>0){

			$data['coin']=$reg_reward;
		}

		$userid=$User->add($data);

        if($reg_reward){
            $insert=array("type"=>'income',"action"=>'reg_reward',"uid"=>$userid,"touid"=>$userid,"giftid"=>0,"giftcount"=>1,"totalcoin"=>$reg_reward,"showid"=>0,"addtime"=>time() );
            M('users_coinrecord')->add($insert);
        }
        
        $code=createCode();
        $code_info=array('uid'=>$userid,'code'=>$code);
        $Agent_code=M('users_agent_code');
        $isexist=$Agent_code
                    ->field("uid")
                    ->where("uid = {$userid}")
                    ->find();
        if($isexist){
            $Agent_code->where("uid = {$userid}")->save($code_info);	
        }else{
            $Agent_code->add($code_info);
        }
            
		$userinfo=$User->where("id='{$userid}'")->find();		
		
		//$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
		//$userinfo['token']=$token;

		//$userinfo['level']=getLevel($userinfo['experience']);
        
        //$this->updateToken($info['id'],$info['token']);

		/*session('uid',$userinfo['id']);
		session('token',$userinfo['token']);
		session('user',$userinfo);*/
            $rs['errmsg']='註冊成功';
            $rs['userid']=$userinfo['id'];
            echo json_encode($rs);
			exit;;				
	}
	public function forget(){
        
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
        
		$user_login=I("mobile");
		$pass=I("pass");
		$code=I("code");
		$countrycode=I("countrycode");//国家代码
	
		if($user_login!=$_SESSION['mobile']){	
            $rs['errno']=1001;
            $rs['errmsg']='手機號碼不一致';
            echo json_encode($rs);
			exit;					
		}

		if($code!=$_SESSION['mobile_code']){
            $rs['errno']=1001;
            $rs['errmsg']='驗證碼錯誤';
            echo json_encode($rs);
			exit;				
			
		}	

		$user_pass=setPass($pass);
		
		$User=M("users");
		$where['user_login']=$user_login;
		$where['country_code']=$countrycode;
		$ifreg=$User->field("id")->where($where)->find();
		if(!$ifreg){
            $rs['errno']=1001;
            $rs['errmsg']='該帳號不存在';
            echo json_encode($rs);
			exit;	
		}				
		$result=$User->where("id='{$ifreg['id']}'")->setField("user_pass",$user_pass);
		if($result!==false){
            echo json_encode($rs);
			exit;
		}else{
            $rs['errno']=10001;
            $rs['errmsg']='該帳號不存在';
            echo json_encode($rs);
			exit;	
		}
	}
	/* 退出 */
	public function logout(){
        
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
        
		session('uid',null);		
		session('token',null);
		session('user',null);

            $rs['errmsg']='退出登入';
            echo json_encode($rs);
			exit;	
	}	
	/* 获取用户信息 */
	public function getLoginUserInfo(){
        $rs=['errno'=>0,'errmsg'=>'','data'=>[]];
        
		$uid=session("uid");			
		if($uid){
            $rs['data']['user']=json_encode(getUserPrivateInfo($uid));
            echo json_encode($rs);
			exit;
		}else{
            $rs['errno']=1001;
            $rs['errmsg']='未登入';
            echo json_encode($rs);
			exit;
		}
		exit;	
	}		

	/*环信私信通过用户名查找用户*/
	public function searchMember(){
        $uid=session('uid');
		if($uid){
			$userName=(int)I("keyword");
            $where['id']=$userName;
			$result=M("users")->where("id <> {$uid}")->where($where)->find();/*不能查找自己*/
			if($result){
				$data=array(
					"code"=>0,
					"msg"=>"",
					"info"=>$result
				);
				}else{
				$data=array(
					"code"=>1,
					"msg"=>"",
					"info"=>""
				);}
		}else{
			$data=array(
				"code"=>2,
				"msg"=>"",
				"info"=>""
			);
		}
		echo json_encode($data);
	}
	/*环信私信功能创建陌生人信息时，通过用户id获取用户的头像和昵称*/

	public function searchUserInfo(){
		$uid=(int)I("uid");
		$user=M("users");
        $where['id']=$uid;
		$info=$user->field('avatar,user_nicename')->where($where)->find();
		if($info){
			$data=array(
			"code"=>0,
			"avatar"=>$info['avatar'],
			"user_nicename"=>$info['user_nicename'],
			"msg"=>""
			);
		}else{
			$data=array(
			"code"=>1,
			"avatar"=>'',
			"user_nicename"=>'',
			"msg"=>""
			);
		}
		echo json_encode($data);
		exit;
	}
	
	/**
	 * 检测拉黑状态
	 * @desc 用于私信聊天时判断私聊双方的拉黑状态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info.u2t  是否拉黑对方,0表示未拉黑，1表示已拉黑
	 * @return string info.t2u  是否被对方拉黑,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	function checkBlack() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());
			$uid=(int)I("uid");
			$touid=(int)I("touid");
			$u2t = isBlack($uid,$touid);
			$t2u = isBlack($touid,$uid);
		 
			$rs['info']['u2t']=$u2t;
			$rs['info']['t2u']=$t2u;
			echo json_encode($rs);
			exit;
	}	
	//三方开启判断
	public function threeparty(){

		$data=array(
			"login_type"=>$this->config['login_type'],
		);
		echo json_encode($data);
		exit;
	}
	//qq第三方登录========
	public function qq() 
	{
		$href=$_SERVER['HTTP_REFERER'];
		cookie('href',$href,3600000);
		$referer = $_SERVER['HTTP_REFERER'];
		session('login_referer', $referer);
		$qc1 = new \QC();
		$qc1->qq_login();
	}
	public function qqCallback()
	{
		import('ORG.API.qqConnectAPI'); 
		$qc = new \QC();
		$token = $qc->qq_callback();
		$openid = $qc->get_openid();
		$qq = new \QC($token, $openid);
		$arr = $qq->get_user_info();
        
        
        $type='qq';
        $openid=$openid;
        $nickname=$arr['nickname'];
        $avatar=$arr['figureurl_qq_2'];
        
        $userinfo=$this->loginByThird($type,$openid,$nickname,$avatar);
        if($userinfo==1001){
            $this->error('該帳號已被禁用');
            exit;
        }

		session('uid',$userinfo['id']);
		session('token',$userinfo['token']);
		session('user',$userinfo);
		$href=$_COOKIE['AJ1sOD_href'];
		echo "<meta http-equiv=refresh content='0; url=$href'>"; 		
	}	
	/**
	微信登陆 
	**/
	public function weixin()
	{
		$getConfigPri=getConfigPri();	
		$getConfigPub=getConfigPub();	
		$pay_url=$getConfigPub['site'];
	//-------配置
		$href=$_SERVER['HTTP_REFERER'];
		cookie('href',$href,3600000);
		$AppID = $getConfigPri['login_wx_pc_appid'];
		$AppSecret = $getConfigPri['login_wx_pc_appsecret'];
		$callback  = $pay_url.'/index.php?g=home&m=User&a=weixin_callback'; //回调地址
		//微信登录
		session_start();
		//-------生成唯一随机串防CSRF攻击
		$state  = md5(uniqid(rand(), TRUE));
		$_SESSION["wx_state"]    = $state; //存到SESSION
		$callback = urlencode($callback);
		$wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
		header("Location: $wxurl");
	}
	/**
	微信登陆回调
	**/
	public function weixin_callback()
	{
		$getConfigPri=getConfigPri();	
		if($_GET['code']!="")
		{
			$AppID = $getConfigPri['login_wx_pc_appid'];
			$AppSecret = $getConfigPri['login_wx_pc_appsecret'];
			$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$arr=json_decode($json,1);
            
            if(isset($arr['errcode'])){
                echo $arr['errmsg'];
				exit;
            }
            
			//得到 access_token 与 openid
			$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$arr=json_decode($json,1);
			//得到 用户资料
			$users=M("users");
			$openid=$arr['openid'];
			//$openid=$arr['unionid'];
            
            $type='wx';
            $openid=$openid;
            $nickname=$arr['nickname'];
            $avatar=$arr['headimgurl'];
            
            $userinfo=$this->loginByThird($type,$openid,$nickname,$avatar);
            if($userinfo==1001){
                $this->error('該帳號已被禁用');
                exit;
            }

			session('uid',$userinfo['id']);
			session('token',$userinfo['token']);
			session('user',$userinfo);
			$href=$_COOKIE['AJ1sOD_href'];
		 	echo "<meta http-equiv=refresh content='0; url=$href'>"; 
		}
	}
	/**
	微博登陆
	**/
	public function weibo(){
		
		$href=$_SERVER['HTTP_REFERER'];
		cookie('href',$href,3600000);
		$getConfigPri=getConfigPri();	
		$getConfigPub=getConfigPub();	
		$WB_AKEY=$getConfigPri['login_sina_pc_akey'];
		$WB_SKEY=$getConfigPri['login_sina_pc_skey'];
		$pay_url=$getConfigPub['site'];
		$WB_CALLBACK_URL=$pay_url."/index.php?g=home&m=User&a=weibo_callback";
		include_once( 'Lib/Extend/libweibo/config.php' );
		include_once( 'Lib/Extend/libweibo/saetv2.ex.class.php' );
		$o = new \SaeTOAuthV2($WB_AKEY,$WB_SKEY);
		$code_url = $o->getAuthorizeURL( $WB_CALLBACK_URL );
		header("location:".$code_url); 
	}
	/**
	微博登陆回调
	**/
	public function weibo_callback(){

		if($_GET['code']!=""){ 

			$getConfigPri=getConfigPri();	
			$getConfigPub=getConfigPub();	
			$WB_AKEY=$getConfigPri['login_sina_pc_akey'];
			$WB_SKEY=$getConfigPri['login_sina_pc_skey'];
			$pay_url=$getConfigPub['site'];
			$WB_CALLBACK_URL=$pay_url."/index.php?g=home&m=User&a=weibo_callback";
			$o = new \SaeTOAuthV2( $WB_AKEY , $WB_SKEY );
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $WB_CALLBACK_URL;
			$token = $o->getAccessToken( 'code', $keys ); 
			$c = new \SaeTClientV2( $WB_AKEY , $WB_SKEY ,$token["access_token"]);
			$ms = $c->home_timeline(); 
			$uid_get = $c->get_uid();
			$uid =  $token['uid'];
			$user_message = $c->show_user_by_id( $token['uid']);
            
            
            $type='sina';
            $openid=$user_message['id'];
            $nickname=$user_message['screen_name'];
            $avatar=$user_message['profile_image_url'];
            
            $userinfo=$this->loginByThird($type,$openid,$nickname,$avatar);
            if($userinfo==1001){
                $this->error('該帳號已被禁用');
                exit;
            }
            
			session('uid',$userinfo['id']);
			session('token',$userinfo['token']);
			session('user',$userinfo);
			$href=$_COOKIE['AJ1sOD_href'];
		 	echo "<meta http-equiv=refresh content='0; url=$href'>"; 

		} 

	}
    
    protected function loginByThird($type,$openid,$nickname,$avatar){
        $Users=M("users");
        $info=$Users
            ->field($this->fields)
            ->where("openid='{$openid}' and login_type='{$type}' and user_type=2")
            ->find();
            
		$configpri=getConfigPri();
		if(!$info){
			/* 注册 */
			$user_pass='yunbaokeji';
			$user_pass=setPass($user_pass);
			$user_login=$type.'_'.time().rand(100,999);

			if(!$nickname){
				$nickname=$type.'用户-'.substr($openid,-4);
			}else{
				$nickname=urldecode($nickname);
			}
			if(!$avatar){
				$avatar='/default.jpg';
				$avatar_thumb='/default_thumb.jpg';
			}else{
				$avatar=urldecode($avatar);
				$avatar_a=explode('/',$avatar);
				$avatar_a_n=count($avatar_a);
				if($type=='qq'){
					$avatar_a[$avatar_a_n-1]='100';
					$avatar_thumb=implode('/',$avatar_a);
				}else if($type=='wx'){
					$avatar_a[$avatar_a_n-1]='64';
					$avatar_thumb=implode('/',$avatar_a);
				}else{
					$avatar_thumb=$avatar;
				}
				
			}
			$reg_reward=$configpri['reg_reward'];
			$data=array(
				'user_login' => $user_login,
				'user_nicename' =>$nickname,
				'user_pass' =>$user_pass,
				'signature' =>'None',
				'avatar' =>$avatar,
				'avatar_thumb' =>$avatar_thumb,
				'last_login_ip' =>get_client_ip(0,true),
				'create_time' => date("Y-m-d H:i:s"),
				'user_status' => 1,
				'openid' => $openid,
				'login_type' => $type, 
				"user_type"=>2,//会员
				"coin"=>$reg_reward,
			);
			
            $uid=$Users->add($data);


            if($reg_reward){
                $insert=array("type"=>'income',"action"=>'reg_reward',"uid"=>$uid,"touid"=>$uid,"giftid"=>0,"giftcount"=>1,"totalcoin"=>$reg_reward,"showid"=>0,"addtime"=>time() );
                M('users_coinrecord')->add($insert);
            }
        
			$code=createCode();
			$code_info=array('uid'=>$uid,'code'=>$code);
            $Agent_code=M('users_agent_code');
			$isexist=$Agent_code
						->field("uid")
						->where("uid = {$uid}")
						->find();
			if($isexist){
				$Agent_code->where("uid = {$uid}")->save($code_info);	
			}else{
				$Agent_code->add($code_info);	
			}
            
			$info['id']=$uid;
			$info['user_nicename']=$data['user_nicename'];
			$info['avatar']=$data['avatar'];
			$info['avatar_thumb']=$data['avatar_thumb'];
			$info['sex']='2';
			$info['signature']=$data['signature'];
			$info['coin']='0';
			$info['login_type']=$data['login_type'];
			$info['province']='';
			$info['city']='';
			$info['birthday']='';
			$info['consumption']='0';
			$info['user_status']=1;
			$info['last_login_time']='';
		}else{
			if(!$avatar){
				$avatar='/default.jpg';
				$avatar_thumb='/default_thumb.jpg';
			}else{
				$avatar=urldecode($avatar);
				$avatar_a=explode('/',$avatar);
				$avatar_a_n=count($avatar_a);
				if($type=='qq'){
					$avatar_a[$avatar_a_n-1]='100';
					$avatar_thumb=implode('/',$avatar_a);
				}else if($type=='wx'){
					$avatar_a[$avatar_a_n-1]='64';
					$avatar_thumb=implode('/',$avatar_a);
				}else{
					$avatar_thumb=$avatar;
				}
				
			}
			
			$info['avatar']=$avatar;
			$info['avatar_thumb']=$avatar_thumb;
			
			$data=array(
				'avatar' =>$avatar,
				'avatar_thumb' =>$avatar_thumb,
			);
			
		}
		
		if($info['user_status']=='0'){
			return 1001;					
		}
		
		$info['isreg']='0';
		$info['isagent']='0';
		if($info['last_login_time']=='' ){
			$info['isreg']='1';
			$info['isagent']='1';
		}

        if($configpri['agent_switch']==0){
            $info['isagent']='0';
        }
		unset($info['last_login_time']);
		
		$info['level']=getLevel($info['consumption']);

		$info['level_anchor']=getLevelAnchor($info['votestotal']);

		$token=md5(md5($info['id'].$openid.time()));
		
		$info['token']=$token;
		$info['avatar']=get_upload_path($info['avatar']);
		$info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
        
        $this->updateToken($info['id'],$info['token']);
        
		
        return $info;    
        
    }
	/* 更新token 登陆信息 */
    protected function updateToken($uid,$token) {
		$expiretime=time()+60*60*24*300;

		M("users")
			->where("id={$uid}")
			->save(array("token"=>$token, "expiretime"=>$expiretime ,'last_login_time' => date("Y-m-d H:i:s"), "last_login_ip"=>get_client_ip(0,true) ));

		$token_info=array(
			'uid'=>$uid,
			'token'=>$token,
			'expiretime'=>$expiretime,
		);
		
		setcaches("token_".$uid,$token_info);
        /* 删除PUSH信息 */
        M("users_pushid")->where("uid={$uid}")->delete();
        
		return 1;
    }
}


