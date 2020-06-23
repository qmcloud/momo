<?php

class Model_Login extends PhalApi_Model_NotORM {

	protected $fields='id,user_nicename,avatar,avatar_thumb,sex,signature,coin,consumption,votestotal,province,city,birthday,user_status,login_type,last_login_time,location,country_code';

	/* 会员登录 */   	
    public function userLogin($user_login,$user_pass,$country_code) {

		$user_pass=setPass($user_pass);
		
		$info=DI()->notorm->users
				->select($this->fields.',user_pass')
				->where('user_login=? and user_type="2"',$user_login) 
				->fetchOne();
		if(!$info || $info['user_pass'] != $user_pass || $info['country_code']!=$country_code){
			return 1001;
		}
		unset($info['user_pass']);
		if($info['user_status']=='0'){
			return 1002;					
		}
		unset($info['user_status']);
		
		$info['isreg']='0';
		$info['isagent']='0';
        
		if($info['last_login_time']==''){
			$info['isreg']='1';
			$info['isagent']='1';
		}
		
        $configpri=getConfigPri();
        if($configpri['agent_switch']==0){
            $info['isagent']='0';
        }
        
		$info['level']=getLevel($info['consumption']);
		$info['level_anchor']=getLevelAnchor($info['votestotal']);

		$token=md5(md5($info['id'].$user_login.time()));
		
		$info['token']=$token;
		$info['avatar']=get_upload_path($info['avatar']);
		$info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
		
		$this->updateToken($info['id'],$token);
		
        return $info;
    }	
	
	/* 会员注册 */
    public function userReg($user_login,$user_pass,$source,$country_code) {

		$user_pass=setPass($user_pass);
		
		$configpri=getConfigPri();
		$reg_reward=$configpri['reg_reward'];
		$data=array(
			'user_login' => $user_login,
			'mobile' =>$user_login,
			'user_nicename' =>'手机用户'.substr($user_login,-4),
			'user_pass' =>$user_pass,
			'signature' =>'这家伙很懒，什么都没留下',
			'avatar' =>'/default.jpg',
			'avatar_thumb' =>'/default_thumb.jpg',
			'last_login_ip' =>$_SERVER['REMOTE_ADDR'],
			'create_time' => date("Y-m-d H:i:s"),
			'user_status' => 1,
			"user_type"=>2,//会员
			"source"=>$source,
			"coin"=>$reg_reward,
			"country_code"=>$country_code,
		);

		$isexist=DI()->notorm->users
				->select('id')
				->where('user_login=?',$user_login) 
				->fetchOne();
		if($isexist){
			return 1006;
		}

		$rs=DI()->notorm->users->insert($data);	
		if(!$rs){
			return 1007;
		}
        $uid=$rs['id'];
        if($reg_reward>0){
            $insert=array("type"=>'income',"action"=>'reg_reward',"uid"=>$uid,"touid"=>$uid,"giftid"=>0,"giftcount"=>1,"totalcoin"=>$reg_reward,"showid"=>0,"addtime"=>time() );
            DI()->notorm->users_coinrecord->insert($insert);
        }
		$code=$this->createCode();
		$code_info=array('uid'=>$uid,'code'=>$code);
		$isexist=DI()->notorm->users_agent_code
					->select("*")
					->where('uid = ?',$uid)
					->fetchOne();
		if($isexist){
			DI()->notorm->users_agent_code->where('uid = ?',$uid)->update($code_info);	
		}else{
			DI()->notorm->users_agent_code->insert($code_info);	
		}
		return 1;
    }	

	/* 找回密码 */
	public function userFindPass($user_login,$user_pass,$country_code){
		$isexist=DI()->notorm->users
				->select('id')
				->where('user_login=? and user_type="2" and country_code=?',$user_login,$country_code) 
				->fetchOne();
		if(!$isexist){
			return 1006;
		}		
		$user_pass=setPass($user_pass);

		return DI()->notorm->users
				->where('id=?',$isexist['id']) 
				->update(array('user_pass'=>$user_pass));
		
	}	
		
	/* 第三方会员登录 */
    public function userLoginByThird($openid,$type,$nickname,$avatar,$source) {			
        $info=DI()->notorm->users
            ->select($this->fields)
            ->where('openid=? and login_type=? ',$openid,$type)
            ->fetchOne();
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
				// $avatar_a=explode('/',$avatar);
				// $avatar_a_n=count($avatar_a);
				// if($type=='qq'){
					// $avatar_a[$avatar_a_n-1]='100';
					// $avatar_thumb=implode('/',$avatar_a);
				// }else if($type=='wx'){
					// $avatar_a[$avatar_a_n-1]='64';
					// $avatar_thumb=implode('/',$avatar_a);
				// }else{
					$avatar_thumb=$avatar;
				// }
				
			}
			$reg_reward=$configpri['reg_reward'];
			$data=array(
				'user_login' => $user_login,
				'user_nicename' =>$nickname,
				'user_pass' =>$user_pass,
				'signature' =>'这家伙很懒，什么都没留下',
				'avatar' =>$avatar,
				'avatar_thumb' =>$avatar_thumb,
				'last_login_ip' =>$_SERVER['REMOTE_ADDR'],
				'create_time' => date("Y-m-d H:i:s"),
				'user_status' => 1,
				'openid' => $openid,
				'login_type' => $type, 
				"user_type"=>2,//会员
				"source"=>$source,
				"coin"=>$reg_reward,
			);
			
			$rs=DI()->notorm->users->insert($data);
            
            $uid=$rs['id'];
            if($reg_reward>0){
                $insert=array("type"=>'income',"action"=>'reg_reward',"uid"=>$uid,"touid"=>$uid,"giftid"=>0,"giftcount"=>1,"totalcoin"=>$reg_reward,"showid"=>0,"addtime"=>time() );
                DI()->notorm->users_coinrecord->insert($insert);
            }

			$code=$this->createCode();
			$code_info=array('uid'=>$uid,'code'=>$code);
			$isexist=DI()->notorm->users_agent_code
						->select("*")
						->where('uid = ?',$uid)
						->fetchOne();
			if($isexist){
				DI()->notorm->users_agent_code->where('uid = ?',$uid)->update($code_info);	
			}else{
				DI()->notorm->users_agent_code->insert($code_info);	
			}
            
			$info['id']=$uid;
			$info['user_nicename']=$data['user_nicename'];
			$info['avatar']=get_upload_path($data['avatar']);
			$info['avatar_thumb']=get_upload_path($data['avatar_thumb']);
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
            /* 更新头像 */
			/* if(!$avatar){
				$avatar='/default.jpg';
				$avatar_thumb='/default_thumb.jpg';
			}else{
				$avatar=urldecode($avatar);
                $avatar_thumb=$avatar;
			}
			
			$info['avatar']=$avatar;
			$info['avatar_thumb']=$avatar_thumb;
			
			$data=array(
				'avatar' =>$avatar,
				'avatar_thumb' =>$avatar_thumb,
			); */
			
		}
		
		if($info['user_status']=='0'){
			return 1001;					
		}
		unset($info['user_status']);
		
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
		
		$this->updateToken($info['id'],$token);
		
        return $info;
    }		
	
	/* 更新token 登陆信息 */
    public function updateToken($uid,$token,$data=array()) {
		$expiretime=time()+60*60*24*300;

		DI()->notorm->users
			->where('id=?',$uid)
			->update(array("token"=>$token, "expiretime"=>$expiretime ,'last_login_time' => date("Y-m-d H:i:s"), "last_login_ip"=>$_SERVER['REMOTE_ADDR'] ));

		$token_info=array(
			'uid'=>$uid,
			'token'=>$token,
			'expiretime'=>$expiretime,
		);
		
		setcaches("token_".$uid,$token_info);		
        
		return 1;
    }	
	
	/* 生成邀请码 */
	public function createCode($len=6,$format='ALL2'){
        $is_abc = $is_numer = 0;
        $password = $tmp =''; 
        switch($format){
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'ALL2':
                $chars='ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars='0123456789';
                break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        
        while(strlen($password)<$len){
            $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
            if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
                $is_numer = 1;
            }
            if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
                $is_abc = 1;
            }
            $password.= $tmp;
        }
        if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
            $password = $this->createCode($len,$format);
        }
        if($password!=''){
            
            $oneinfo=DI()->notorm->users_agent_code
	            ->select("uid")
	            ->where("code=?",$password)
	            ->fetchOne();
	        
            if(!$oneinfo){
                return $password;
            }            
        }
        $password = $this->createCode($len,$format);
        return $password;
    }
    
    /* 更新极光ID */
    public function upUserPush($uid,$pushid){
        
        $isexist=DI()->notorm->users_pushid
                    ->select('*')
                    ->where('uid=?',$uid)
                    ->fetchOne();
        if(!$isexist){
            DI()->notorm->users_pushid->insert(array('uid'=>$uid,'pushid'=>$pushid));
        }else if($isexist['pushid']!=$pushid){
            DI()->notorm->users_pushid->where('uid=?',$uid)->update(array('pushid'=>$pushid));
        }
        return 1;
    }
}
