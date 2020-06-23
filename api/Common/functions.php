<?php

	/* Redis链接 */
	function connectionRedis(){
		$REDIS_HOST= DI()->config->get('app.REDIS_HOST');
		$REDIS_AUTH= DI()->config->get('app.REDIS_AUTH');
		$REDIS_PORT= DI()->config->get('app.REDIS_PORT');
		$redis = new Redis();
		$redis -> pconnect($REDIS_HOST,$REDIS_PORT);
		$redis -> auth($REDIS_AUTH);

		return $redis;
	}
	/* 设置缓存 */
	function setcache($key,$info){
		$config=getConfigPri();
		if($config['cache_switch']!=1){
			return 1;
		}

		DI()->redis->set($key,json_encode($info));
		DI()->redis->expire($key, $config['cache_time']); 

		return 1;
	}	
	/* 设置缓存 可自定义时间*/
	function setcaches($key,$info,$time=0){
		DI()->redis->set($key,json_encode($info));
        if($time > 0){
            DI()->redis->expire($key, $time); 
        }
		
		return 1;
	}
	/* 获取缓存 */
	function getcache($key){
		$config=getConfigPri();

		if($config['cache_switch']!=1){
			$isexist=false;
		}else{
			$isexist=DI()->redis->Get($key);
		}

		return json_decode($isexist,true);
	}		
	/* 获取缓存 不判断后台设置 */
	function getcaches($key){

		$isexist=DI()->redis->Get($key);
		
		return json_decode($isexist,true);
	}
	/* 删除缓存 */
	function delcache($key){
		$isexist=DI()->redis->del($key);
		return 1;
	}	
    
	/* 同系统函数 array_column   php版本低于5.5.0 时用  */
	function array_column2($input, $columnKey, $indexKey = NULL){
		$columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
		$indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
		$indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
		$result = array();
 
		foreach ((array)$input AS $key => $row){ 
			if ($columnKeyIsNumber){
				$tmp = array_slice($row, $columnKey, 1);
				$tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
			}else{
				$tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
			}
			if (!$indexKeyIsNull){
				if ($indexKeyIsNumber){
					$key = array_slice($row, $indexKey, 1);
					$key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
					$key = is_null($key) ? 0 : $key;
				}else{
					$key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
				}
			}
			$result[$key] = $tmp;
		}
		return $result;
	}
	
	/* 密码检查 */
	function passcheck($user_pass) {
		$num = preg_match("/^[a-zA-Z]+$/",$user_pass);
		$word = preg_match("/^[0-9]+$/",$user_pass);
		$check = preg_match("/^[a-zA-Z0-9]{6,12}$/",$user_pass);
		if($num || $word ){
			return 2;
		}else if(!$check){
			return 0;
		}		
		return 1;
	}
	/* 检验手机号 */
	function checkMobile($mobile){
		$ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/",$mobile);
		if($ismobile){
			return 1;
		}else{
			return 1;
		}
	}
	/* 随机数 */
	function random($length = 6 , $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}
	/* 发送验证码--互译无线 */
	function sendCode_huiyi($mobile,$code){
		$rs=array();
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }
        
		/* 互亿无线 */
		$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
		$content="您的验证码是：".$code."。请不要把验证码泄露给其他人。";
		$post_data = "account=".$config['ihuyi_account']."&password=".$config['ihuyi_ps']."&mobile=".$mobile."&content=".rawurlencode($content);
		//密码可以使用明文密码或使用32位MD5加密
		$gets = xml_to_array(Post($post_data, $target));
        file_put_contents(API_ROOT.'/../data/sendCode_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data:'.$post_data."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/../data/sendCode_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 gets:'.json_encode($gets)."\r\n",FILE_APPEND);
		if($gets['SubmitResult']['code']==2){
            setSendcode(array('type'=>'1','account'=>$mobile,'content'=>$content));
			$rs['code']=0;
		}else{
			$rs['code']=1002;
			//$rs['msg']=$gets['SubmitResult']['msg'];
			$rs['msg']="获取失败";
		} 
		return $rs;
	}

	function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
	
	function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
	/* 发送验证码 */
    
    	/* 发送验证码 -- 容联云 */
	function sendCode($mobile,$code,$country_code){
        
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }
        
        require_once API_ROOT.'/../sdk/ronglianyun/CCPRestSDK.php';
        
        //主帐号
        $accountSid= $config['ccp_sid'];
        //主帐号Token
        $accountToken= $config['ccp_token'];
        //应用Id
        $appId=$config['ccp_appid'];
        //请求地址，格式如下，不需要写https://
        $serverIP='app.cloopen.com';
        //请求端口 
        $serverPort='8883';
        //REST版本号
        $softVersion='2013-12-26';
		file_put_contents('./zzz1212.txt',date('y-m-d h:i:s').'country_code========= :'.json_encode($country_code)."\r\n",FILE_APPEND);
      
        $tempId=$config['ccp_tempid'];
		// if($country_code!='86' && $country_code!='0'){
		if($country_code!='86'){
			$appId=$config['country_ccp_appid'];//国际短信应用
			$tempId=$config['country_ccp_tempid'];//国际短信应用
			
			$mobile='00'.$country_code.$mobile;
		}
	
        file_put_contents(API_ROOT.'/../data/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data: accountSid:'.$accountSid.";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",FILE_APPEND);

        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        
        $datas=[];
        $datas[]=$code;
		
         file_put_contents('./zzz1212.txt',date('y-m-d h:i:s').'mobile========= :'.json_encode($mobile)."\r\n",FILE_APPEND);
        $result = $rest->sendTemplateSMS($mobile,$datas,$tempId);
        file_put_contents(API_ROOT.'/../data/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
        file_put_contents('./zzz1212.txt',date('y-m-d h:i:s').'result========= :'.json_encode($result)."\r\n",FILE_APPEND);
         if($result == NULL ) {
            $rs['code']=1002;
			$rs['msg']="获取失败";
            return $rs;
         }
         if($result->statusCode!=0) {
            //echo "error code :" . $result->statusCode . "<br>";
            //echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
            $rs['code']=1002;
			//$rs['msg']=$gets['SubmitResult']['msg'];
			$rs['msg']="获取失败";
            return $rs;
         }
        $content=$code;
        setSendcode(array('type'=>'1','account'=>$mobile,'content'=>$content));

		return $rs;
	}
    
    /* curl get请求 */
    function curl_get($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
    
	/* 检测文件后缀 */
	function checkExt($filename){
		$config=array("jpg","png","jpeg");
		$ext   =   pathinfo(strip_tags($filename), PATHINFO_EXTENSION);
		 
		return empty($config) ? true : in_array(strtolower($ext), $config);
	}	    
	/* 密码加密 */
	function setPass($pass){
		$authcode='rCt52pF2cnnKNB3Hkp';
		$pass="###".md5(md5($authcode.$pass));
		return $pass;
	}	
    /* 去除NULL 判断空处理 主要针对字符串类型*/
	function checkNull($checkstr){
        $checkstr=trim($checkstr);
		$checkstr=urldecode($checkstr);
        if(get_magic_quotes_gpc()==0){
			$checkstr=addslashes($checkstr);
		}
		//$checkstr=htmlspecialchars($checkstr);
		//$checkstr=filterEmoji($checkstr);
		if( strstr($checkstr,'null') || (!$checkstr && $checkstr!=0 ) ){
			$str='';
		}else{
			$str=$checkstr;
		}
		return $str;	
	}
	/* 去除emoji表情 */
	function filterEmoji($str){
		$str = preg_replace_callback(
			'/./u',
			function (array $match) {
				return strlen($match[0]) >= 4 ? '' : $match[0];
			},
			$str);
		return $str;
	}	
	/* 公共配置 */
	function getConfigPub() {
		$key='getConfigPub';
		$config=getcaches($key);
		if(!$config){
			$config= DI()->notorm->options
					->select('option_value')
					->where("option_name='configpub'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
			setcaches($key,$config);
		}
        if(is_array($config['live_time_coin'])){
            
        }else if($config['live_time_coin']){
            $config['live_time_coin']=preg_split('/,|，/',$config['live_time_coin']);
        }else{
            $config['live_time_coin']=array();
        }
            
        if(is_array($config['login_type'])){
            
        }else if($config['login_type']){
            $config['login_type']=preg_split('/,|，/',$config['login_type']);
        }else{
            $config['login_type']=array();
        }
        
        if(is_array($config['share_type'])){
            
        }else if($config['share_type']){
            $config['share_type']=preg_split('/,|，/',$config['share_type']);
        }else{
            $config['share_type']=array();
        }
    
        if(is_array($config['live_type'])){
            
        }else if($config['live_type']){
            $live_type=preg_split('/,|，/',$config['live_type']);
            foreach($live_type as $k=>$v){
                $live_type[$k]=preg_split('/;|；/',$v);
            }
            $config['live_type']=$live_type;
        }else{
            $config['live_type']=array();
        }
		return 	$config;
	}		
	
	/* 私密配置 */
	function getConfigPri() {
		$key='getConfigPri';
		$config=getcaches($key);
		if(!$config){
			$config= DI()->notorm->options
					->select('option_value')
					->where("option_name='configpri'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
			setcaches($key,$config);
		}
	
        
		return 	$config;
	}		
	
	/**
	 * 返回带协议的域名
	 */
	function get_host(){
		$config=getConfigPub();
		return $config['site'];
	}	
	
	/**
	 * 转化数据库保存的文件路径，为可以访问的url
	 */
	function get_upload_path($file){
        if($file==''){
            return $file;
        }
		if(strpos($file,"http")===0){
			return html_entity_decode($file);
		}else if(strpos($file,"/")===0){
			$filepath= get_host().$file;
			return html_entity_decode($filepath);
		}else{
			$space_host= DI()->config->get('app.Qiniu.space_host');
			$filepath=$space_host."/".$file;
			return html_entity_decode($filepath);
		}
	}
	
	/* 判断是否关注 */
	function isAttention($uid,$touid) {
		$isexist=DI()->notorm->users_attention
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			return  '1';
		}
        return  '0';
	}
	/* 是否黑名单 */
	function isBlack($uid,$touid) {	
		$isexist=DI()->notorm->users_black
				->select("*")
				->where('uid=? and touid=?',$uid,$touid)
				->fetchOne();
		if($isexist){
			return '1';
		}
        return '0';
	}	
	
	/* 判断权限 */
	function isAdmin($uid,$liveuid) {
		if($uid==$liveuid){
			return 50;
		}
		$isuper=isSuper($uid);
		if($isuper){
			return 60;
		}
		$isexist=DI()->notorm->users_livemanager
					->select("*")
					->where('uid=? and liveuid=?',$uid,$liveuid)
					->fetchOne();
		if($isexist){
			return  40;
		}
		
		return  30;
			
	}	
	/* 判断账号是否超管 */
	function isSuper($uid){
		$isexist=DI()->notorm->users_super
					->select("*")
					->where('uid=?',$uid)
					->fetchOne();
		if($isexist){
			return 1;
		}			
		return 0;
	}
	/* 判断token */
	function checkToken($uid,$token) {
		$userinfo=getcaches("token_".$uid);
		if(!$userinfo){
			$userinfo=DI()->notorm->users
						->select('token,expiretime')
						->where('id = ? and user_type="2"', $uid)
						->fetchOne();	
			setcaches("token_".$uid,$userinfo);
		}

		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
			return 700;				
		}else{
			return 	0;				
		} 		
	}	
	
	/* 用户基本信息 */
	function getUserInfo($uid,$type=0) {
		$info=getcaches("userinfo_".$uid);
		if(!$info){
			$info=DI()->notorm->users
					->select('id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,user_status,issuper,location')
					->where('id=? and user_type="2"',$uid)
					->fetchOne();	
			if($info){
				$info['level']=(string)getLevel($info['consumption']);
				$info['level_anchor']=(string)getLevelAnchor($info['votestotal']);
				$info['avatar']=get_upload_path($info['avatar']);
				$info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
			}else if($type==1){
                return 	$info;
                
            }else{
                $info['id']=$uid;
                $info['user_nicename']='用户不存在';
                $info['avatar']=get_upload_path('/default.jpg');
                $info['avatar_thumb']=get_upload_path('/default_thumb.jpg');
                $info['sex']='0';
                $info['signature']='';
                $info['consumption']='0';
                $info['votestotal']='0';
                $info['province']='';
                $info['city']='';
                $info['birthday']='';
                $info['issuper']='0';
                $info['level']='1';
                $info['level_anchor']='1';
            }
            if($info){
                setcaches("userinfo_".$uid,$info);
            }
			
		}
        if($info){
            $info['vip']=getUserVip($uid);
            $info['liang']=getUserLiang($uid);
        }
		return 	$info;		
	}
	
	/* 会员等级 */
    
	function getLevelList(){
        $key='level';
		$level=getcaches($key);
		if(!$level){
			$level=DI()->notorm->experlevel
					->select("*")
					->order("level_up asc")
					->fetchAll();
            foreach($level as $k=>$v){
                $v['thumb']=get_upload_path($v['thumb']);
                if($v['colour']){
                    $v['colour']='#'.$v['colour'];
                }else{
                    $v['colour']='#ffdd00';
                }
                $level[$k]=$v;
            }
			setcaches($key,$level);			 
		}
        
        return $level;
    }
	function getLevel($experience){
		$levelid=1;
        $level_a=1;
		$level=getLevelList();

		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}else{
				$level_a = $v['levelid'];
			}
		}
		$levelid = $levelid < $level_a ? $level_a:$levelid;
		return (string)$levelid;
	}
	/* 主播等级 */
	function getLevelAnchorList(){
		$key='levelanchor';
		$level=getcaches($key);
		if(!$level){
			$level=DI()->notorm->experlevel_anchor
					->select("*")
					->order("level_up asc")
					->fetchAll();
            foreach($level as $k=>$v){
                $v['thumb']=get_upload_path($v['thumb']);
                $v['thumb_mark']=get_upload_path($v['thumb_mark']);
                $level[$k]=$v;
            }
			setcaches($key,$level);			 
		}
        
        return $level;
    }
	function getLevelAnchor($experience){
		$levelid=1;
		$level_a=1;
        $level=getLevelAnchorList();

		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}else{
				$level_a = $v['levelid'];
			}
		}
		$levelid = $levelid < $level_a ? $level_a:$levelid;
		return (string)$levelid;
	}

	/* 统计 直播 */
	function getLives($uid) {
		/* 直播中 */
		$count1=DI()->notorm->users_live
				->where('uid=? and islive="1"',$uid)
				->count();
		/* 回放 */
		$count2=DI()->notorm->users_liverecord
					->where('uid=? ',$uid)
					->count();
		return 	$count1+$count2;
	}		
	
	/* 统计 关注 */
	function getFollows($uid) {
		$count=DI()->notorm->users_attention
				->where('uid=? ',$uid)
				->count();
		return 	$count;
	}			
	
	/* 统计 粉丝 */
	function getFans($uid) {
		$count=DI()->notorm->users_attention
				->where('touid=? ',$uid)
				->count();
		return 	$count;
	}		
	/**
	*  @desc 根据两点间的经纬度计算距离
	*  @param float $lat 纬度值
	*  @param float $lng 经度值
	*/
	function getDistance($lat1, $lng1, $lat2, $lng2){
		$earthRadius = 6371000; //近似地球半径 单位 米
		 /*
		   Convert these degrees to radians
		   to work with the formula
		 */

		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;

		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;


		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;
		
		$distance=$calculatedDistance/1000;
		if($distance<10){
			$rs=round($distance,2);
		}else if($distance > 1000){
			$rs='1000';
		}else{
			$rs=round($distance);
		}
		return $rs.'km';
	}
	/* 判断账号是否禁用 */
	function isBan($uid){
		$status=DI()->notorm->users
					->select("user_status")
					->where('id=?',$uid)
					->fetchOne();
		if(!$status || $status['user_status']==0){
			return '0';
		}
		return '1';
	}
	/* 是否认证 */
	function isAuth($uid){
		$status=DI()->notorm->users_auth
					->select("status")
					->where('uid=?',$uid)
					->fetchOne();
		if($status && $status['status']==1){
			return '1';
		}

		return '0';
	}
	/* 过滤字符 */
	function filterField($field){
		$configpri=getConfigPri();
		
		$sensitive_field=$configpri['sensitive_field'];
		
		$sensitive=explode(",",$sensitive_field);
		$replace=array();
		$preg=array();
		foreach($sensitive as $k=>$v){
			if($v!=''){
				$re='';
				$num=mb_strlen($v);
				for($i=0;$i<$num;$i++){
					$re.='*';
				}
				$replace[$k]=$re;
				$preg[$k]='/'.$v.'/';
			}else{
				unset($sensitive[$k]);
			}
		}
		
		return preg_replace($preg,$replace,$field);
	}
	/* 时间差计算 */
	function datetime($time){
		$cha=time()-$time;
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
		if($cha<60){
			return $cha.'秒前';
		}else if($iz<60){
			return $iz.'分钟前';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟前';
		}else if($dz<30){
			return $dz.'天前';
		}else{
			return date("Y-m-d",$time);
		}
	}		
	/* 时长格式化 */
	function getSeconds($cha,$type=0){	 
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
        if($type==1){
            if($s<10){
                $s='0'.$s;
            }
            if($i<10){
                $i='0'.$i;
            }

            if($h<10){
                $h='0'.$h;
            }
            
            if($hz<10){
                $hz='0'.$hz;
            }
            return $hz.':'.$i.':'.$s;
        }
        
        
		if($cha<60){
			return $cha.'秒';
		}else if($iz<60){
			return $iz.'分钟'.$s.'秒';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟'.$s.'秒';
		}else if($dz<30){
			return $dz.'天'.$h.'小时'.$i.'分钟'.$s.'秒';
		}
	}	

	/* 数字格式化 */
	function NumberFormat($num){
		if($num<10000){

		}else if($num<1000000){
			$num=round($num/10000,2).'万';
		}else if($num<100000000){
			$num=round($num/10000,1).'万';
		}else if($num<10000000000){
			$num=round($num/100000000,2).'亿';
		}else{
			$num=round($num/100000000,1).'亿';
		}
		return $num;
	}

	/**
	*  @desc 获取推拉流地址
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKeyA($host,$stream,$type){
		$configpri=getConfigPri();
		$cdn_switch=$configpri['cdn_switch'];
		//$cdn_switch=3;
		switch($cdn_switch){
			case '1':
				$url=PrivateKey_ali($host,$stream,$type);
				break;
			case '2':
				$url=PrivateKey_tx($host,$stream,$type);
				break;
			case '3':
				$url=PrivateKey_qn($host,$stream,$type);
				break;
			case '4':
				$url=PrivateKey_ws($host,$stream,$type);
				break;
			case '5':
				$url=PrivateKey_wy($host,$stream,$type);
				break;
			case '6':
				$url=PrivateKey_ady($host,$stream,$type);
				break;
		}

		
		return $url;
	}
	
	/**
	*  @desc 阿里云直播A类鉴权
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_ali($host,$stream,$type){
		$configpri=getConfigPri();
		$push=$configpri['push_url'];
		$pull=$configpri['pull_url'];
		$key_push=$configpri['auth_key_push'];
		$length_push=$configpri['auth_length_push'];
		$key_pull=$configpri['auth_key_pull'];
		$length_pull=$configpri['auth_length_pull'];
        
		if($type==1){
			$domain=$host.'://'.$push;
			$time=time() + $length_push;
		}else{
			$domain=$host.'://'.$pull;
			$time=time() + $length_pull;
		}
		
		$filename="/5showcam/".$stream;

		if($type==1){
            if($key_push!=''){
                $sstring = $filename."-".$time."-0-0-".$key_push;
                $md5=md5($sstring);
                $auth_key="auth_key=".$time."-0-0-".$md5;
            }
			if($auth_key){
				$auth_key='?'.$auth_key;
			}
			$url=$domain.$filename.$auth_key;
		}else{
            if($key_pull!=''){
                $sstring = $filename."-".$time."-0-0-".$key_pull;
                $md5=md5($sstring);
                $auth_key="auth_key=".$time."-0-0-".$md5;
            }
			if($auth_key){
				$auth_key='?'.$auth_key;
			}
			$url=$domain.$filename.$auth_key;
		}
		
		return $url;
	}
	
	/**
	*  @desc 腾讯云推拉流地址
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_tx($host,$stream,$type){
		$configpri=getConfigPri();
		$bizid=$configpri['tx_bizid'];
		$push_url_key=$configpri['tx_push_key'];
		$push=$configpri['tx_push'];
		$pull=$configpri['tx_pull'];
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
		$ext = $stream_a[1];
		
		//$live_code = $bizid . "_" .$streamKey;      	
		$live_code = $streamKey;      	
		$now_time = time() + 3*60*60;
		$txTime = dechex($now_time);

		$txSecret = md5($push_url_key . $live_code . $txTime);
		$safe_url = "&txSecret=" .$txSecret."&txTime=" .$txTime;		

		if($type==1){
			//$push_url = "rtmp://" . $bizid . ".livepush2.myqcloud.com/live/" .  $live_code . "?bizid=" . $bizid . "&record=flv" .$safe_url;	可录像
			$url = "rtmp://{$push}/live/" . $live_code . "?bizid=" . $bizid . "" .$safe_url;	
		}else{
			$url = "http://{$pull}/live/" . $live_code . ".flv";
		}
		
		return $url;
	}

	/**
	*  @desc 七牛云直播
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_qn($host,$stream,$type){
		
		$configpri=getConfigPri();
		$ak=$configpri['qn_ak'];
		$sk=$configpri['qn_sk'];
		$hubName=$configpri['qn_hname'];
		$push=$configpri['qn_push'];
		$pull=$configpri['qn_pull'];
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
		$ext = $stream_a[1];

		if($type==1){
			$time=time() +60*60*10;
			//RTMP 推流地址
			$url = \Qiniu\Pili\RTMPPublishURL($push, $hubName, $streamKey, $time, $ak, $sk);
		}else{
			if($ext=='flv'){
				$pull=str_replace('pili-live-rtmp','pili-live-hdl',$pull);
				//HDL 直播地址
				$url = \Qiniu\Pili\HDLPlayURL($pull, $hubName, $streamKey);
			}else if($ext=='m3u8'){
				$pull=str_replace('pili-live-rtmp','pili-live-hls',$pull);
				//HLS 直播地址
				$url = \Qiniu\Pili\HLSPlayURL($pull, $hubName, $streamKey);
			}else{
				//RTMP 直播放址
				$url = \Qiniu\Pili\RTMPPlayURL($pull, $hubName, $streamKey);
			}
		}
				
		return $url;
	}
	
	/**
	*  @desc 网宿推拉流
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_ws($host,$stream,$type){
		$configpri=getConfigPri();
		if($type==1){
			$domain=$host.'://'.$configpri['ws_push'];
			//$time=time() +60*60*10;
		}else{
			$domain=$host.'://'.$configpri['ws_pull'];
			//$time=time() - 60*30 + $configpri['auth_length'];
		}
		
		$filename="/".$configpri['ws_apn']."/".$stream;

		$url=$domain.$filename;
		
		return $url;
	}
	
	/**网易cdn获取拉流地址**/
	function PrivateKey_wy($host,$stream,$type){
		$configpri=getConfigPri();
		$appkey=$configpri['wy_appkey'];
		$appSecret=$configpri['wy_appsecret'];
		$nonce =rand(1000,9999);
		$curTime=time();
		$var=$appSecret.$nonce.$curTime;
		$checkSum=sha1($appSecret.$nonce.$curTime);
		
		$header =array(
			"Content-Type:application/json;charset=utf-8",
			"AppKey:".$appkey,
			"Nonce:" .$nonce,
			"CurTime:".$curTime,
			"CheckSum:".$checkSum,
		);
        if($type==1){
            $url='https://vcloud.163.com/app/channel/create';
            $paramarr = array(
                "name"  =>$stream,
                "type"  =>0,
            );
        }else{
            $url='https://vcloud.163.com/app/address';
            $paramarr = array(
                "cid"  =>$stream,
            );
        }
        $paramarr=json_encode($paramarr);

		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_HEADER, 0);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $header); 
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_POST, 1);
		curl_setopt($curl,CURLOPT_POSTFIELDS, $paramarr);
		$data = curl_exec($curl);
		curl_close($curl);
		$rs=json_decode($data,1);
		return $rs;
	}
	
	/**
	*  @desc 奥点云推拉流
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_ady($host,$stream,$type){
		$configpri=getConfigPri();
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
		$ext = $stream_a[1];

		if($type==1){
			$domain=$host.'://'.$configpri['ady_push'];
			//$time=time() +60*60*10;
			$filename="/".$configpri['ady_apn'].'/'.$stream;
			$url=$domain.$filename;
		}else{
			if($ext=='m3u8'){
				$domain=$host.'://'.$configpri['ady_hls_pull'];
				//$time=time() - 60*30 + $configpri['auth_length'];
				$filename="/".$configpri['ady_apn']."/".$stream;
				$url=$domain.$filename;
			}else{
				$domain=$host.'://'.$configpri['ady_pull'];
				//$time=time() - 60*30 + $configpri['auth_length'];
				$filename="/".$configpri['ady_apn']."/".$stream;
				$url=$domain.$filename;
			}
		}
				
		return $url;
	}

	/* 获取用户VIP */
	function getUserVip($uid){
		$rs=array(
			'type'=>'0',
		);
		$nowtime=time();
		$key='vip_'.$uid;
		$isexist=getcaches($key);
		if(!$isexist){
			$isexist=DI()->notorm->users_vip
						->select("*")
						->where('uid=?',$uid)
						->fetchOne();			
			if($isexist){
				setcaches($key,$isexist);
			}
		}

		if($isexist){
			if($isexist['endtime'] <= $nowtime){
				return $rs;
            }
			$rs['type']='1';
		}
		
		return $rs;
	}

	/* 获取用户坐骑 */
	function getUserCar($uid){
		$rs=array(
			'id'=>'0',
			'swf'=>'',
			'swftime'=>'0',
			'words'=>'',
		);
		$nowtime=time();
		
		$key='car_'.$uid;
		$isexist=getcaches($key);
		if(!$isexist){
			$isexist=DI()->notorm->users_car
						->select("*")
						->where('uid=? and status=1',$uid)
						->fetchOne();
			if($isexist){
				setcaches($key,$isexist);
			}
        }
		if($isexist){
			if($isexist['endtime']<= $nowtime){
				return $rs;
			}
			$key2='carinfo';
			$car_list=getcaches($key2);
			if(!$car_list){
				$car_list=DI()->notorm->car
					->select("*")
                    ->order("orderno asc")
					->fetchAll();
				if($car_list){
					setcaches($key2,$car_list);
				}
			}
			$info=array();
			if($car_list){
				foreach($car_list as $k=>$v){
					if($v['id']==$isexist['carid']){
						$info=$v;
					}	
				}
				
				if($info){
					$rs['id']=$info['id'];
					$rs['swf']=get_upload_path($info['swf']) ;
					$rs['swftime']=$info['swftime'];
					$rs['words']=$info['words'];
                }
			}
			
		}
		
		return $rs;
	}

	/* 获取用户靓号 */
	function getUserLiang($uid){
		$rs=array(
			'name'=>'0',
		);
		$nowtime=time();
		$key='liang_'.$uid;
		$isexist=getcaches($key);
		if(!$isexist){
			$isexist=DI()->notorm->liang
						->select("*")
						->where('uid=? and status=1 and state=1',$uid)
						->fetchOne();	
			if($isexist){
				setcaches($key,$isexist);
			}
		}
		if($isexist){
			$rs['name']=$isexist['name'];
		}
		
		return $rs;
	}
	
	/* 三级分销 */
	function setAgentProfit($uid,$total){
				/* 分销 */
		$distribut1=0;
		$distribut2=0;
		$distribut3=0;
		$configpri=getConfigPri();
		if($configpri['agent_switch']==1){
			$agent=DI()->notorm->users_agent
				->select("*")
				->where('uid=?',$uid)
				->fetchOne();
			$isinsert=0;
			/* 一级 */
			if($agent['one_uid'] && $configpri['distribut1']){
				$distribut1=$total*$configpri['distribut1']*0.01;
                if($distribut1>0){
                    $profit=DI()->notorm->users_agent_profit
                        ->select("*")
                        ->where('uid=?',$agent['one_uid'])
                        ->fetchOne();
                    if($profit){
                        DI()->notorm->users_agent_profit
                            ->where('uid=?',$agent['one_uid'])
                            ->update(array('one_profit' => new NotORM_Literal("one_profit + {$distribut1}")));
                    }else{
                        DI()->notorm->users_agent_profit
                            ->insert(array('uid'=>$agent['one_uid'],'one_profit' =>$distribut1 ));
                    }
                    DI()->notorm->users
                            ->where('id=?',$agent['one_uid'])
                            ->update(array('votes' => new NotORM_Literal("votes + {$distribut1}")));
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'income',
                        'action'=>'agentprofit',
                        'uid'=>$agent['one_uid'],
                        'votes'=>$distribut1,
                        'addtime'=>time(),
                    ];
                    DI()->notorm->users_voterecord->insert($insert_votes);
                }
			}
			/* 二级 */
			if($agent['two_uid'] && $configpri['distribut2']){
				$distribut2=$total*$configpri['distribut2']*0.01;
                if($distribut2>0){
                    $profit=DI()->notorm->users_agent_profit
                        ->select("*")
                        ->where('uid=?',$agent['two_uid'])
                        ->fetchOne();
                    if($profit){
                        DI()->notorm->users_agent_profit
                            ->where('uid=?',$agent['two_uid'])
                            ->update(array('two_profit' => new NotORM_Literal("two_profit + {$distribut2}")));
                    }else{
                        DI()->notorm->users_agent_profit
                            ->insert(array('uid'=>$agent['two_uid'],'two_profit' =>$distribut2 ));
                    }
                    DI()->notorm->users
                            ->where('id=?',$agent['two_uid'])
                            ->update(array('votes' => new NotORM_Literal("votes + {$distribut2}")));
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'income',
                        'action'=>'agentprofit',
                        'uid'=>$agent['two_uid'],
                        'votes'=>$distribut2,
                        'addtime'=>time(),
                    ];
                    DI()->notorm->users_voterecord->insert($insert_votes);
                }
			}
			/* 三级 */
			/* if($agent['three_uid'] && $configpri['distribut3']){
				$distribut3=$total*$configpri['distribut3']*0.01;
                if($distribut3>0){
                    $profit=DI()->notorm->users_agent_profit
                        ->select("*")
                        ->where('uid=?',$agent['three_uid'])
                        ->fetchOne();
                    if($profit){
                        DI()->notorm->users_agent_profit
                            ->where('uid=?',$agent['three_uid'])
                            ->update(array('three_profit' => new NotORM_Literal("three_profit + {$distribut3}")));
                    }else{
                        DI()->notorm->users_agent_profit
                            ->insert(array('uid'=>$agent['three_uid'],'three_profit' =>$distribut3 ));
                    }
                    DI()->notorm->users
                            ->where('id=?',$agent['three_uid'])
                            ->update(array('votes' => new NotORM_Literal("votes + {$distribut3}")));
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'income',
                        'action'=>'agentprofit',
                        'uid'=>$agent['three_uid'],
                        'votes'=>$distribut3,
                        'addtime'=>time(),
                    ];
                    DI()->notorm->users_voterecord->insert($insert_votes);
                }
			} */
			
			if($isinsert==1){
				$data=array(
					'uid'=>$uid,
					'total'=>$total,
					'one_uid'=>$agent['one_uid'],
					'two_uid'=>$agent['two_uid'],
					'three_uid'=>$agent['three_uid'],
					'one_profit'=>$distribut1,
					'two_profit'=>$distribut2,
					'three_profit'=>$distribut3,
					'addtime'=>time(),
				);
				
				DI()->notorm->users_agent_profit_recode->insert( $data );
				
			}
		}
		return 1;
		
	}
    
    /* 家族分成 */
    function setFamilyDivide($liveuid,$total){
        $configpri=getConfigPri();
	
		$anthor_total=$total;
		/* 家族 */
		if($configpri['family_switch']==1){
			$users_family=DI()->notorm->users_family
							->select("familyid,divide_family")
							->where('uid=? and state=2',$liveuid)
							->fetchOne();

			if($users_family){
				$familyinfo=DI()->notorm->family
							->select("uid,divide_family")
							->where('id=?',$users_family['familyid'])
							->fetchOne();
                if($familyinfo){
                    $divide_family=$familyinfo['divide_family'];

                    /* 主播 */
                    if( $users_family['divide_family']>=0){
                        $divide_family=$users_family['divide_family'];
                        
                    }
                    $family_total=$total * $divide_family * 0.01;
                    
                        $anthor_total=$total - $family_total;
                        $addtime=time();
                        $time=date('Y-m-d',$addtime);
                        DI()->notorm->family_profit
                               ->insert(array("uid"=>$liveuid,"time"=>$time,"addtime"=>$addtime,"profit"=>$family_total,"profit_anthor"=>$anthor_total,"total"=>$total,"familyid"=>$users_family['familyid']));

                    if($family_total){
                        
                        DI()->notorm->users
                                ->where('id = ?', $familyinfo['uid'])
                                ->update( array( 'votes' => new NotORM_Literal("votes + {$family_total}")  ));
                                
                        $insert_votes=[
                            'type'=>'income',
                            'action'=>'familyprofit',
                            'uid'=>$familyinfo['uid'],
                            'votes'=>$family_total,
                            'addtime'=>time(),
                        ];
                        DI()->notorm->users_voterecord->insert($insert_votes);
                    }
                }
			}	
		}
        return $anthor_total;
    }
	
	/* ip限定 */
	function ip_limit(){
		$configpri=getConfigPri();
		if($configpri['iplimit_switch']==0){
			return 0;
		}
		$date = date("Ymd");
		$ip= ip2long($_SERVER["REMOTE_ADDR"]) ; 
		
		$isexist=DI()->notorm->getcode_limit_ip
				->select('ip,date,times')
				->where(' ip=? ',$ip) 
				->fetchOne();
		if(!$isexist){
			$data=array(
				"ip" => $ip,
				"date" => $date,
				"times" => 1,
			);
			$isexist=DI()->notorm->getcode_limit_ip->insert($data);
			return 0;
		}elseif($date == $isexist['date'] && $isexist['times'] >= $configpri['iplimit_times'] ){
			return 1;
		}else{
			if($date == $isexist['date']){
				$isexist=DI()->notorm->getcode_limit_ip
						->where(' ip=? ',$ip) 
						->update(array('times'=> new NotORM_Literal("times + 1 ")));
				return 0;
			}else{
				$isexist=DI()->notorm->getcode_limit_ip
						->where(' ip=? ',$ip) 
						->update(array('date'=> $date ,'times'=>1));
				return 0;
			}
		}	
	}	
    
    /* 验证码记录 */
    function setSendcode($data){
        if($data){
            $data['addtime']=time();
            DI()->notorm->sendcode->insert($data);
        }
    }

    /* 检测用户是否存在 */
    function checkUser($where){
        if($where==''){
            return 0;
        }

        $isexist=DI()->notorm->users->where($where)->fetchOne();
        
        if($isexist){
            return 1;
        }
        
        return 0;
    }
    
    /* 直播分类 */
    function getLiveClass(){
        $key="getLiveClass";
		$list=getcaches($key);
		if(!$list){
            $list=DI()->notorm->live_class
					->select("*")
                    ->order("orderno asc,id desc")
					->fetchAll();
            foreach($list as $k=>$v){
                $list[$k]['thumb']=get_upload_path($v['thumb']);
            }
			setcaches($key,$list); 
		}
        return $list;        
        
    }
    /* 校验签名 */
    function checkSign($data,$sign){
        $key=DI()->config->get('app.sign_key');
        $str='';
        ksort($data);
        foreach($data as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        $str.=$key;
        $newsign=md5($str);
        
        if($sign==$newsign){
            return 1;
        }
        return 0;
    }
    /* 用户退出，注销PUSH */
    function userLogout($uid){
        $list=DI()->notorm->users_pushid
                ->where('uid=?',$uid)
                ->delete();
        return 1;
    }
    
	/*获取音乐信息*/
	function getMusicInfo($user_nicename,$musicid){

		$res=DI()->notorm->users_music->select("id,title,author,img_url,length,file_url,use_nums")->where("id=?",$musicid)->fetchOne();

		if(!$res){
			$res=array();
			$res['id']='0';
			$res['title']='';
			$res['author']='';
			$res['img_url']='';
			$res['length']='00:00';
			$res['file_url']='';
			$res['use_nums']='0';
			$res['music_format']='@'.$user_nicename.'创作的原声';

		}else{
			$res['music_format']=$res['title'].'--'.$res['anchor'];
			$res['img_url']=get_upload_path($res['img_url']);
			$res['file_url']=get_upload_path($res['file_url']);
		}

		

		return $res;

	}

	/*距离格式化*/
	function distanceFormat($distance){
		if($distance<1000){
			return $distance.'米';
		}else{

			if(floor($distance/10)<10){
				return number_format($distance/10,1);  //保留一位小数，会四舍五入
			}else{
				return ">10千米";
			}
		}
	}

	/* 视频是否点赞 */
	function ifLike($uid,$videoid){
		$like=DI()->notorm->users_video_like
				->select("id")
				->where("uid='{$uid}' and videoid='{$videoid}'")
				->fetchOne();
		if($like){
			return 1;
		}else{
			return 0;
		}	
	}

	/* 视频是否踩 */
	function ifStep($uid,$videoid){
		$like=DI()->notorm->users_video_step
				->select("id")
				->where("uid='{$uid}' and videoid='{$videoid}'")
				->fetchOne();
		if($like){
			return 1;
		}else{
			return 0;
		}	
	}

	/* 腾讯COS处理 */
    function setTxUrl($url){
        
        if(!strstr($url,'myqcloud')){
            return $url;
        }
        
        $url_a=parse_url($url);
        
        $file=$url_a['path'];
        $signkey='Shanghai0912'; //腾讯云后台设置（控制台->存储桶->域名管理->CDN鉴权配置->鉴权Key）
        $now_time = time();
        $sign=md5($signkey.$file.$now_time);
        
        return $url.'?sign='.$sign.'&t='.$now_time;
        
    }
    
    /* 拉黑视频名单 */
	function getVideoBlack($uid){
		$videoids=array('0');
		$list=DI()->notorm->users_video_black
						->select("videoid")
						->where("uid='{$uid}'")
						->fetchAll();
		if($list){
			$videoids=array_column($list,'videoid');
		}
		
		$videoids_s=implode(",",$videoids);
		
		return $videoids_s;
	}

    /* 生成二维码 */
    
    function scerweima($url=''){

        $key=md5($url);
        
        //生成二维码图片
        $filename2 = '/data/upload/qr/'.$key.'.png';
        $filename = API_ROOT.'/../data/upload/qr/'.$key.'.png';
        
        if(!file_exists($filename)){
            require_once API_ROOT.'/../simplewind/Lib/Util/phpqrcode.php';
            
            $value = $url;					//二维码内容
            
            $errorCorrectionLevel = 'L';	//容错级别 
            $matrixPointSize = 6.2068965517241379310344827586207;			//生成图片大小  
            
            //生成二维码图片
            \QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2); 
        }
      
        return $filename2;
    }
    
    /* 奖池信息 */
    function getJackpotInfo(){
        $jackpotinfo = DI()->notorm->jackpot->where("id = 1 ") -> fetchOne();
        return $jackpotinfo;
    }
    
    /* 奖池配置 */
    function getJackpotSet(){
        $key='jackpotset';
		$config=getcaches($key);
		if(!$config){
			$config= DI()->notorm->options
					->select('option_value')
					->where("option_name='jackpot'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
            if($config){
                setcaches($key,$config);
            }
			
		}
		return 	$config;
    }
    
    /* 奖池等级设置 */
    function getJackpotLevelList(){
        $key='jackpot_level';
        $list=getcaches($key);
        if(!$list){
            $list= DI()->notorm->jackpot_level->order("level_up asc")->fetchAll();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    }

    /* 奖池等级 */
    function getJackpotLevel($experience){
        $levelid='0';

		$level=getJackpotLevelList();

		foreach($level as $k=>$v){
			if( $v['level_up']<=$experience){
				$levelid=$v['levelid'];
			}
		}

		return (string)$levelid;
    }
    /* 奖池中奖配置 */
    function getJackpotRate(){
        $key='jackpot_rate';
        $list=getcaches($key);
        if(!$list){
            $list= DI()->notorm->jackpot_rate->order("id desc")->fetchAll();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    }

    /* 幸运礼物中奖配置 */
    function getLuckRate(){
        $key='luck_rate';
        $list=getcaches($key);
        if(!$list){
            $list= DI()->notorm->luck_rate->order("id desc")->fetchAll();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    }
    
    /* 视频数据处理 */
    function handleVideo($uid,$v){
        
			$userinfo=getUserInfo($v['uid']);
			if(!$userinfo){
				$userinfo['user_nicename']="已删除";
			}

			$v['userinfo']=$userinfo;
			$v['datetime']=datetime($v['addtime']);	
			$v['addtime']=date('Y-m-d H:i:s',$v['addtime']);	
			$v['comments']=NumberFormat($v['comments']);	
			$v['likes']=NumberFormat($v['likes']);	
			$v['steps']=NumberFormat($v['steps']);	
            
            $v['islike']='0';	
            $v['isstep']='0';	
            $v['isattent']='0';
            
			if($uid>0){
				$v['islike']=(string)ifLike($uid,$v['id']);	
				$v['isstep']=(string)ifStep($uid,$v['id']);	
			}
            
            if($uid>0 && $uid!=$v['uid']){
                $v['isattent']=(string)isAttention($uid,$v['uid']);	
            }
            
			$v['thumb']=get_upload_path($v['thumb']);
			$v['thumb_s']=get_upload_path($v['thumb_s']);
			$v['href']=get_upload_path($v['href']);
            
            $v['ad_url']=get_upload_path($v['ad_url']);

            if($v['ad_endtime']<time()){
                $v['ad_url']='';
            }
            
            /* 商品 */
            $goodsinfo=(object)[];
           
            $v['goodsinfo']=$goodsinfo;
            
			unset($v['ad_endtime']);
			unset($v['orderno']);
			unset($v['isdel']);
			unset($v['show_val']);
			unset($v['status']);
			unset($v['xiajia_reason']);
			unset($v['nopass_time']);
			unset($v['watch_ok']);

        return $v;
    }