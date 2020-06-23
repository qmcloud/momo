<?php

/* 前台 */

	/* redis链接 */
	function connectionRedis(){
		$REDIS_HOST= C('REDIS_HOST');
		$REDIS_AUTH= C('REDIS_AUTH');
		$REDIS_PORT= C('REDIS_PORT');
		$redis = new \Redis();
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
		$redis=connectionRedis();
		$redis->set($key,json_encode($info));
		$redis->expire($key, $config['cache_time']); 
		
		return 1;
	}	
	/* 设置缓存 可自定义时间*/
	function setcaches($key,$info,$time=0){

		$redis=connectionRedis();
		$redis->set($key,json_encode($info));
        if($time > 0){
            $redis->expire($key, $time); 
        }
		
		return 1;
	}
	/* 获取缓存 */
	function getcache($key){
		$config=getConfigPri();

		$redis=connectionRedis();
		$isexist=$redis->Get($key);
		if($config['cache_switch']!=1){
			$isexist=false;
		}
		
		return json_decode($isexist,true);
	}		
	/* 获取缓存 不判断后台设置 */
	function getcaches($key){
		$redis=connectionRedis();
		$isexist=$redis->Get($key);

		
		return json_decode($isexist,true);
	}
	/* 删除缓存 */
	function delcache($key){
		$redis=connectionRedis();
		$isexist=$redis->del($key);
		
		return 1;
	}	
	
	/* 去除NULL 判断空处理 主要针对字符串类型*/
	function checkNull($checkstr){
		$checkstr=urldecode($checkstr);
		$checkstr=htmlspecialchars($checkstr);
		$checkstr=trim($checkstr);

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

	/* 获取公共配置 */
	function getConfigPub() {
		$key='getConfigPub';
		$config=getcaches($key);
		if(!$config){
			$config=M("options")->where("option_name='configpub'")->getField("option_value");
            $config=json_decode($config,true);
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
	
	/* 获取私密配置 */
	function getConfigPri() {
		$key='getConfigPri';
		$config=getcaches($key);
		if(!$config){
			$config=M("options")->where("option_name='configpri'")->getField("option_value");
            $config=json_decode($config,true);
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
			return $file;
		}else if(strpos($file,"/")===0){
			$filepath= get_host().$file;
			return $filepath;
		}else{
			
            $space_host=get_host();
            $config=M("options")->where("option_name='cmf_settings'")->getField("option_value");
            $config=json_decode($config,true);
            
            if($config && $config['storage']['type']=='Qiniu'){
                $space_host='http://'.$config['storage']['Qiniu']['domain'];
            }
            $filepath=$space_host.'/'.$file;
			return $filepath;
		}
	}	
	/* 获取等级 */
	function getLevelList(){
        $key='level';
		$level=getcaches($key);
		if(!$level){
			$level= M("experlevel")->order("level_up asc")->select();
            
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
		
		return $levelid;
	}
	/* 主播等级 */
	function getLevelAnchorList(){
		$key='levelanchor';
		$level=getcaches($key);
		if(!$level){
			$level= M("experlevel_anchor")->order("level_up asc")->select();
            foreach($level as $k=>$v){
                $v['thumb']=get_upload_path($v['thumb']);
                $v['thumb_mark']=get_upload_path($v['thumb_mark']);
            }
			setcaches($key,$level);			 
		}
        
        return $level;
    }
	function getLevelAnchor($experience){
		$levelid=1;
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
		
		return $levelid;
	}

	/* 判断是否关注 */
	function isAttention($uid,$touid) {
		$id=M("users_attention")->where("uid='$uid' and touid='$touid'")->find();
		if($id){
			return  1;
		}else{
			return  0;
		}			 	
	}
	/*判断是否拉黑*/ 
	function isBlack($uid,$touid){
		$isexist=M("users_black")->where("uid=".$uid." and touid=".$touid)->find();
		if($isexist){
			return 1;
		}else{
			return 0;					
		}
	}
	/* 关注人数 */
	function getFollownums($uid) 
	{
		return M("users_attention")->where("uid='{$uid}' ")->count();
	}
	/* 粉丝人数 */
	function getFansnums($uid) 
	{
		return M("users_attention")->where(" touid='{$uid}'")->count();
	} 
	/* 用户基本信息 */
    function getUserInfo($uid) {
        $info= M("users")->field("id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,issuper")->where("id='{$uid}'")->find();
		if($info){
			$info['avatar']=get_upload_path($info['avatar']);
			$info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
			$info['level']=getLevel($info['consumption']);
			$info['level_anchor']=getLevelAnchor($info['votestotal']);

			$info['vip']=getUserVip($uid);
			$info['liang']=getUserLiang($uid);
        }else{    
            $info['id']=$uid;
            $info['user_nicename']='用户不存在';
            $info['avatar']=get_upload_path('/default.jpg');
            $info['avatar_thumb']=get_upload_path('/default_thumb.jpg');
            $info['coin']='0';
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
				
		return 	$info;		
    }		 
	/*获取收到礼物数量(tsd) 以及送出的礼物数量（tsc） */
	function getgif($uid)
	{
		
    $live=M("users_coinrecord");
		$count=$live->query('select sum(case when touid='.$uid.' then 1 else 0 end) as tsd,sum(case when uid='.$uid.' then 1 else 0 end) as tsc from cmf_users_coinrecord');
		return 	$count;		
	}
	/* 用户信息 含有私密信息 */
   function getUserPrivateInfo($uid) {
        $info= M("users")->field('id,user_login,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,coin,votes,token,birthday,issuper')->where("id='{$uid}'")->find();
		if($info){
			$info['lighttime']="0";
			$info['light']=0;
			$info['level']=getLevel($info['consumption']);
			$info['level_anchor']=getLevelAnchor($info['votestotal']);
			$info['avatar']=get_upload_path($info['avatar']);
			$info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
			
			$info['vip']=getUserVip($uid);
			$info['liang']=getUserLiang($uid);
		}
		return 	$info;		
    }			
		
		/* 用户信息 含有私密信息 */
    function getUserToken($uid) {
		$info= M("users")->field('token')->where("id='{$uid}'")->find();
		return 	$info['token'];		
    }				
	/* 房间管理员 */
	function getIsAdmin($uid,$showid){
		if($uid==$showid){		
			return 50;
		}
		$isuper=isSuper($uid);
		if($isuper){
			return 60;
		}
		$id=M("users_livemanager")->where("uid = '$uid' and liveuid = '$showid'")->find();

		if($id)	{
			return 40;					
		}
		return 30;		
	}
	/*判断token是否过期*/
	function checkToken($uid,$token)
	{
		if(!$uid || !$token){
            session('uid',null);		
            session('token',null);
            session('user',null);
            cookie('uid',null);
            cookie('token',null);
			return 700;	
		}
		$userinfo=getcaches("token_".$uid);
		if(!$userinfo){
			$userinfo=M("users")->field('token,expiretime')->where("id =".$uid." and user_type='2'")->find();	
			setcaches("token_".$uid,$userinfo);								
		}
		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
            session('uid',null);		
            session('token',null);
            session('user',null);
            cookie('uid',null);
            cookie('token',null);
			return 700;				
		}else{
			return 	0;				
		} 
	}
	/*前台个人中心判断是否登录*/
	function LogIn()
	{
		$uid=session("uid");
		if($uid<=0)
		{
			$url=$_SERVER['HTTP_HOST'];
			header("Location:http://".$url); 
			exit;
		}
	}
	/* 判断账号是否超管 */
	function isSuper($uid){
		$isexist=M("users_super")->where("uid='{$uid}'")->find();
		if($isexist){
			return 1;
		}			
		return 0;
	}
	/* 判断账号是被禁用 */
	function isBan($uid){
		$status=M("users")->field("user_status")->where("id=".$uid)->find();
		if(!$status || $status['user_status']==0){
			return 0;
		}
		return 1;
	}
	
	/* 过滤关键词 */
	function filterField($field){
		$configpri=getConfigPri();
		
		$sensitive_field=$configpri['sensitive_field'];
		
		$sensitive=explode(",",$sensitive_field);
		$replace=array();
		$preg=array();
		foreach($sensitive as $k=>$v){
			if($v){
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
	
	/* 检验手机号 */
	function checkMobile($mobile){
		$ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/",$mobile);
		if($ismobile){
			return 1;
		}else{
			return 0;
		}
	}
	
	/* 多维数组排序 */
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
	/*直播间判断是否开启僵尸粉*/
	function isZombie($uid)
	{
		$userinfo=M("users")->field("iszombie")->where("id=".$uid)->find();
		return $userinfo['iszombie'];		
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
    
	/*判断该用户是否已经认证*/
	function auth($uid)
	{
		$users_auth=M("users_auth")->field('uid,status')->where("uid=".$uid)->find();
		if($users_auth)
		{
			return $users_auth["status"];
		}

   
        return 3;

	}

	/* 获取指定长度的随机字符串 */
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
	
	
	/* 发送验证码 */
	function sendCode_huyi($mobile,$code){
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
        file_put_contents(SITE_PATH.'../data/sendCode_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 gets:'.json_encode($gets)."\r\n",FILE_APPEND);
		if($gets['SubmitResult']['code']==2){
            setSendcode(array('type'=>'1','account'=>$mobile,'content'=>$content));
			$rs['code']=0;
		}else{
			$rs['code']=1002;
			$rs['msg']=$gets['SubmitResult']['msg'];
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
	function sendCode($mobile,$code){
        
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }
        
        require_once SITE_PATH.'../sdk/ronglianyun/CCPRestSDK.php';
        
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
        
        $tempId=$config['ccp_tempid'];
        
        file_put_contents(SITE_PATH.'../data/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data: accountSid:'.$accountSid.";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",FILE_APPEND);

        $rest = new \REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        
        $datas=[];
        $datas[]=$code;
        
        $result = $rest->sendTemplateSMS($mobile,$datas,$tempId);
        file_put_contents(SITE_PATH.'../data/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
        
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
	
	/**导出Excel 表格
   * @param $expTitle 名称
   * @param $expCellName 参数
   * @param $expTableData 内容
   * @throws \PHPExcel_Exception
   * @throws \PHPExcel_Reader_Exception
   */
	function exportExcel($expTitle,$expCellName,$expTableData,$cellName)
	{
		$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
		$fileName = date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);
		vendor("PHPExcel.PHPExcel");
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
		}
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
			}
		}
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel5为xls格式，excel2007为xlsx格式
		$objWriter->save('php://output');
		exit;
	}
	/* 密码加密 */
	function setPass($pass){
		$authcode='rCt52pF2cnnKNB3Hkp';
		$pass="###".md5(md5($authcode.$pass));
		return $pass;
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
			//$domain.$filename.'?vhost='.$configpri['pull_url'].$auth_key;
			$url=array(
				'cdn'=>urlencode($domain.'/5showcam'),
				'stream'=>urlencode($stream.$auth_key),
			);
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
			
			if($type==3){
				$url_a=explode('/'.$stream,$url);
				$url=array(
					'cdn'=>urlencode($url_a[0]),
					'stream'=>urlencode($stream.$url_a[1]),
				);
			}
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
			//$url = "rtmp://" . $bizid .".livepush2.myqcloud.com/live/" . $live_code . "?bizid=" . $bizid . "" .$safe_url;
			$url=array(
				'cdn'=>urlencode("rtmp://{$push}/live/"),
				'stream'=>urlencode($live_code."?bizid=".$bizid."".$safe_url),
			);
		}else{
			$url = "http://{$pull}/live/" . $live_code . ".".$ext;
			
			if($type==3){
				$url_a=explode('/'.$live_code,$url);
				$url=array(
					'cdn'=>urlencode($url_a[0]),
					'stream'=>urlencode($live_code.$url_a[1]),
				);
			}
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
		require_once SITE_PATH.'../api/public/qiniucdn/Pili_v2.php';
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
			$url2 = \Qiniu\Pili\RTMPPublishURL($push, $hubName, $streamKey, $time, $ak, $sk);
			$url_a=explode('/',$url2);
			//return $url_a;
			$url=array(
				'cdn'=>urlencode($url_a[0].'//'.$url_a[2].'/'.$url_a[3]),
				'stream'=>urlencode($url_a[4]),
			);
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
			if($type==3){
				$url_a=explode('/'.$stream,$url);
				$url=array(
					'cdn'=>urlencode($url_a[0]),
					'stream'=>urlencode($stream.$url_a[1]),
				);
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
			$filename="/".$configpri['ws_apn'];
			$url=array(
				'cdn'=>urlencode($domain.$filename),
				'stream'=>urlencode($stream),
			);
		}else{
			$domain=$host.'://'.$configpri['ws_pull'];
			//$time=time() - 60*30 + $configpri['auth_length'];
			$filename="/".$configpri['ws_apn']."/".$stream;
			$url=$domain.$filename;
			if($type==3){
				$url_a=explode('/'.$stream,$url);
				$url=array(
					'cdn'=>urlencode($url_a[0]),
					'stream'=>urlencode($stream.$url_a[1]),
				);
			}
		}
		return $url;
	}
	
	/**网易cdn获取拉流地址**/
	function PrivateKey_wy($host,$stream,$type)
	{
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
		$url=json_decode($data,1);
		return $url;
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
			$filename="/".$configpri['ady_apn'];
			$url=array(
				'cdn'=>urlencode($domain.$filename),
				'stream'=>urlencode($stream),
			);
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
			
			if($type==3){
				$url_a=explode('/'.$stream,$url);
				$url=array(
					'cdn'=>urlencode($url_a[0]),
					'stream'=>urlencode($stream.$url_a[1]),
				);
			}
		}
				
		return $url;
	}
	
	/* 生成邀请码 */
	function createCode($len=6,$format='ALL2'){
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
            $password = createCode($len,$format);
        }
        if($password!=''){
            
            $oneinfo=M("users_agent_code")->field("uid")->where("code='{$password}'")->find();
            if(!$oneinfo){
                return $password;
            }            
        }
  
        $password = createCode($len,$format);
        return $password;
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
	/* 数字格式化 不保留小数*/
	function NumberFormat2($num){
		if($num<10000){
			$num=round($num);
		}else if($num<100000000){
			$num=round($num/10000).'万';
		}else{
			$num=round($num/100000000).'亿';
		}
		return $num;
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
			$isexist=M("users_vip")->where("uid={$uid}")->find();		
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
			$isexist=M("users_car")->where("uid={$uid} and status=1")->find();
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
				$car_list=M("car")->order("orderno asc")->select();
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
			$isexist=M("liang")->where("uid={$uid} and status=1 and state=1")->find();
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
			$agent=M("users_agent")->where("uid={$uid}")->find();
			$isinsert=0;
			/* 一级 */
			if($agent['one_uid'] && $configpri['distribut1']){
				$distribut1=$total*$configpri['distribut1']*0.01;
                if($distribut1>0){
                    $profit=M("users_agent_profit")->where("uid={$agent['one_uid']}")->find();
                    if($profit){
                        M()->execute("update __PREFIX__users_agent_profit set one_profit=one_profit+{$distribut1} where uid='{$agent['one_uid']}'");
                    }else{
                        M("users_agent_profit")->add(array('uid'=>$agent['one_uid'],'one_profit' =>$distribut1 ));
                    }
                    M()->execute("update __PREFIX__users set votes=votes+{$distribut1} where id='{$agent['one_uid']}'");
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'income',
                        'action'=>'agentprofit',
                        'uid'=>$agent['one_uid'],
                        'votes'=>$distribut1,
                        'addtime'=>time(),
                    ];
                    M('users_voterecord')->add($insert_votes);
                }
			}
			/* 二级 */
			if($agent['two_uid'] && $configpri['distribut2']){
				$distribut2=$total*$configpri['distribut2']*0.01;
                if($distribut2>0){
                    $profit=M("users_agent_profit")->where("uid={$agent['two_uid']}")->find();
                    if($profit){
                        M()->execute("update __PREFIX__users_agent_profit set two_profit=two_profit+{$distribut2} where uid='{$agent['two_uid']}'");
                    }else{
                        M("users_agent_profit")->add(array('uid'=>$agent['two_uid'],'two_profit' =>$distribut2 ));
                    }
                    M()->execute("update __PREFIX__users set votes=votes+{$distribut2} where id='{$agent['two_uid']}'");
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'income',
                        'action'=>'agentprofit',
                        'uid'=>$agent['two_uid'],
                        'votes'=>$distribut2,
                        'addtime'=>time(),
                    ];
                    M('users_voterecord')->add($insert_votes);
                }
			}
			/* 三级 */
			/* if($agent['three_uid'] && $configpri['distribut3']){
				$distribut3=$total*$configpri['distribut3']*0.01;
                if($distribut3>0){
                    $profit=M("users_agent_profit")->where("uid={$agent['three_uid']}")->find();
                    if($profit){
                        M()->execute("update __PREFIX__users_agent_profit set three_profit=three_profit+{$distribut3} where uid='{$agent['three_uid']}'");
                    }else{
                        M("users_agent_profit")->add(array('uid'=>$agent['three_uid'],'three_profit' =>$distribut3 ));
                    }
                    M()->execute("update __PREFIX__users set votes=votes+{$distribut3} where id='{$agent['three_uid']}'");
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'income',
                        'action'=>'agentprofit',
                        'uid'=>$agent['three_uid'],
                        'votes'=>$distribut3,
                        'addtime'=>time(),
                    ];
                    M('users_voterecord')->add($insert_votes);
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
				M("users_agent_profit_recode")->add($data);
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
			$users_family=M('users_family')
							->field("familyid,divide_family")
							->where("uid={$liveuid} and state=2")
							->find();

			if($users_family){
				$familyinfo=M('family')
							->field("uid,divide_family")
							->where('id='.$users_family['familyid'])
							->find();
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
                        M('family_profit')
                               ->add(array("uid"=>$liveuid,"time"=>$time,"addtime"=>$addtime,"profit"=>$family_total,"profit_anthor"=>$anthor_total,"total"=>$total,"familyid"=>$users_family['familyid']));

                    if($family_total){
                        M()->execute("update __PREFIX__users set votes=votes+{$family_total} where id='{$familyinfo['uid']}'");
                        $insert_votes=[
                            'type'=>'income',
                            'action'=>'familyprofit',
                            'uid'=>$familyinfo['uid'],
                            'votes'=>$family_total,
                            'addtime'=>time(),
                        ];
                        M('users_voterecord')->add($insert_votes);
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
		$ip= ip2long(get_client_ip(0,true)) ; 
		$IP_limit=M("getcode_limit_ip");
		$isexist=$IP_limit->field('ip,date,times')->where("ip={$ip}")->find();
		if(!$isexist){
			$data=array(
				"ip" => $ip,
				"date" => $date,
				"times" => 1,
			);
			$isexist=$IP_limit->add($data);
			return 0;
		}elseif($date == $isexist['date'] && $isexist['times'] >= $configpri['iplimit_times'] ){
			return 1;
		}else{
			if($date == $isexist['date']){
				$isexist=$IP_limit->where("ip={$ip}")->setInc('times',1);
				return 0;
			}else{
				$isexist=$IP_limit->where("ip={$ip}")->save(array('date'=> $date ,'times'=>1));
				return 0;
			}
		}	
	}	
    
    /* 验证码记录 */
    function setSendcode($data){
        if($data){
            $data['addtime']=time();
            M('sendcode')->add($data);
        }
    }

    /* 验证码记录 */
    function checkUser($where){
        if($where==''){
            return 0;
        }

        $isexist=M('users')->field('id')->where($where)->find();
        
        if($isexist){
            return 1;
        }
        
        return 0;
    }
    
    
	function LangT($key, $params = array()){
		
		//$rs = isset(LANGUAGE[$key]) ? LANGUAGE[$key] : $key;
        $rs=$key;
        $names = array_keys($params);
		
		if($names){
			foreach($names as $k=>$v){
				$names[$k]='{' . $v . '}';
			}
		}
        //$names = array_map('formatVa111r', $names);
        return str_replace($names, array_values($params), $rs);
	}	
    
    /* 管理员操作日志 */
    function setAdminLog($action){
        $data=array(
            'adminid'=>$_SESSION['ADMIN_ID'],
            'admin'=>$_SESSION['name'],
            'action'=>$action,
            'ip'=>ip2long(get_client_ip(0,true)),
            'addtime'=>time(),
        );
        
        M("admin_log")->add($data);
        return !0;
    }

    /*获取用户总的送出钻石数*/
	function getSendCoins($uid){
		$sum=M("users_coinrecord")->where("type='expend' and (action='sendbarrage' or action='sendgift') and uid={$uid}")->sum("totalcoin");
		return number_format($sum);
	}
    
    
    /* 印象标签 */
    function getImpressionLabel(){
        
        $key="getImpressionLabel";
		$list=getcaches($key);
		if(!$list){
            $list=M('impression_label')
				->order("orderno asc,id desc")
				->select();
            foreach($list as $k=>$v){
                $list[$k]['colour']='#'.$v['colour'];
            }
                
			setcaches($key,$list); 
		}

        return $list;
    }       

    /* 获取某人的标签 */
    function getMyLabel($uid){
        
        $key="getMyLabel_".$uid;
		$rs=getcaches($key);
		if(!$rs){
            $rs=array();
            $list=M("users_label")
                    ->field("label")
                    ->where("touid={$uid}")
                    ->select();
            $label=array();
            foreach($list as $k=>$v){
                $v_a=preg_split('/,|，/',$v['label']);
                $v_a=array_filter($v_a);
                if($v_a){
                    $label=array_merge($label,$v_a);
                }
            }

            if(!$label){
                return $rs;
            }
            

            $label_nums=array_count_values($label);
            
            $label_key=array_keys($label_nums);
            
            $labels=getImpressionLabel();
            
            $order_nums=array();
            foreach($labels as $k=>$v){
                if(in_array($v['id'],$label_key)){
                    $v['nums']=(string)$label_nums[$v['id']];
                    $order_nums[]=$v['nums'];
                    $rs[]=$v;
                }
            }
            
            array_multisort($order_nums,SORT_DESC,$rs);
        
			setcaches($key,$rs); 
		}

        return $rs;
        
    }   

    /* 获取用户本场贡献 */
    function getContribut($uid,$liveuid,$showid){
        $sum=M("users_coinrecord")
				->where("action='sendgift' and uid={$uid} and touid={$liveuid} and showid={$showid} ")
				->sum('totalcoin');
        if(!$sum){
            $sum=0;
        }
        
        return (string)$sum;
    }
    /* 获取用户守护信息 */
    function getUserGuard($uid,$liveuid){
        $rs=array(
            'type'=>'0',
            'endtime'=>'0',
        );
        $key='getUserGuard_'.$uid.'_'.$liveuid;
        $guardinfo=getcaches($key);
        if(!$guardinfo){
            $guardinfo=M('guard_users')
					->field('type,endtime')
					->where("uid = {$uid} and liveuid={$liveuid}")
					->find();    
            setcaches($key,$guardinfo);
        }
        $nowtime=time();
                    
        if($guardinfo && $guardinfo['endtime']>$nowtime){
            $rs=array(
                'type'=>$guardinfo['type'],
                'endtime'=>$guardinfo['endtime'],
                'endtime_date'=>date("Y.m.d",$guard['endtime']),
            );
        }
        return $rs;
    }
    
    
    /* 对象转数组 */
    function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
     
        return $obj;
    }
    
    /* 分类路径处理 */
    function setpath($id){
        $len=strlen($id);
        $s='';
        for($i=$len;$i<8;$i++){
            $s.='0';
        }
        $path=$s.$id.';';
        
        return $path;
    }
    
    /* 奖池信息 */
    function getJackpotInfo(){
        $jackpotinfo = M('jackpot')->where("id = 1 ") -> find();
        return $jackpotinfo;
    }
    
    /* 奖池配置 */
    function getJackpotSet(){
        $key='jackpotset';
		$config=getcaches($key);
		if(!$config){
			$config= M('options')
					->field('option_value')
					->where("option_name='jackpot'")
					->find();
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
            $list= M('jackpot_level')->order("level_up asc")->select();
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
            $list= M('jackpot_rate')->order("id desc")->select();
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
            $list= M('luck_rate')->order("id desc")->select();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    } 
