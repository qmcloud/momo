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
 * 直播页面
 */
class ShowController extends HomebaseController {
  //首页
	public function index(){
		
		$uid=(int)session("uid");
		$token=session("token");
		$config=$this->config;
		if($config['maintain_switch']==1){
			$this->assign('jumpUrl',__APP__);
			$this->error(nl2br($config['maintain_tips']));
		}
		$getConfigPri=getConfigPri();
		
		$this->assign("configj",json_encode($config));
		$this->assign("getConfigPri",$getConfigPri);
		$User=M('users');
		$Gift=M('gift');
		$Live=M('users_live');
		$Car=M('car');
		$Coinrecord=M('users_coinrecord');
		$nowtime=time();		
		/* 主播信息 */
		$anchorid=(int)I("roomnum");

		$anchorinfo=getUserInfo($anchorid);

		if(!$anchorinfo){
			$this->error('主播不存在');
		}
        

		/*$anchorinfo['level']=getLevel($anchorinfo['consumption']);
		$anchorinfo['anchor_level']=getLevelAnchor($anchorinfo['votestotal']);*/
		$anchorinfo['follows']=getFollownums($anchorinfo['id']);
		$anchorinfo['fans']=getFansnums($anchorinfo['id']);
		$anchorinfo['send_coins']=getSendCoins($anchorinfo['id']);

		/*设置stream*/
		if($uid==$anchorinfo['id']){
			$stream=$anchorid."_".time();
		}else{
			$stream=$anchorid."_".$anchorid;
		}

		$anchorinfo['stream']=$stream;
		$this->assign("anchorinfo",$anchorinfo);
		$this->assign("anchorinfoj",json_encode($anchorinfo) );	
		$liveinfo=$Live->where("uid='{$anchorinfo['id']}' and islive=1")->order("islive desc")->limit(1)->find();
		if($liveinfo['isvideo']==0){
            if($this->configpri['cdn_switch']!=5){
                $liveinfo['pull']=PrivateKeyA('rtmp',$liveinfo['stream'],0);
            }
		}
		$this->assign("liveinfo",$liveinfo);
		$this->assign("liveinfoj",json_encode($liveinfo));
        $usertype=10;
		if($uid>0){
            $checkToken=checkToken($uid,$token);
            if($checkToken==700)
            {
                $this->assign('jumpUrl',__APP__);
				$this->error('登录信息过期，请重新登录');
            }
			/*是否踢出房间*/
            $where['uid']=$uid;
            $where['liveuid']=$anchorid;
            $isexist=M('live_kick')
					->field("id")
					->where($where)
					->find();
            if($isexist){
                $this->assign('jumpUrl',__APP__);
				$this->error('您已被踢出房间');
            }
                    
			$redis = connectionRedis();
            
            $isexist=M('live_shut')
                ->where($where)
                ->find();
            if($isexist){
                $redis -> hSet($anchorid . 'shutup',$uid,1);
            }else{
                $redis -> hDel($anchorid . 'shutup',$uid);
            }
			
			/*身份判断*/
			$usertype=getIsAdmin($uid,$anchorinfo['id']);
			/*该主播是否被禁用*/
			$isBan=isBan($anchorinfo['id']);

			if($isBan==0){

				$this->assign('jumpUrl',__APP__);
				$this->error('该主播已经被禁止直播');
			}

			$isBan=isBan($uid);
			if($isBan==0){
				$this->assign('jumpUrl',__APP__);
				$this->error('你的账号已经被禁用');
			}

			/*进入房间设置redis*/
			$userinfo=$User->where("id=".$uid)->field("id,issuper")->find();
			if($userinfo['issuper']==1){
				$redis  -> hset('super',$userinfo['id'],'1');
			}else{
				$redis  -> hDel('super',$userinfo['id']);
			}
			$redis -> close();

		}

		$this->assign('usertype',$usertype);
		/* 是否关注 */
		$isattention=isAttention($uid,$anchorinfo['id']);
		$this->assign("isattention",$isattention);
		$attention_type = $isattention ? "已关注" : "+关注";
		$this->assign("attention_type",$attention_type);
		$this->assign("anchorid",$anchorid);
		/* 礼物信息 */
		$giftinfo=$Gift->field("*")->order("orderno asc")->select();
        foreach($giftinfo as $k=>$v){
            $v['gifticon']=get_upload_path($v['gifticon']);
            $v['swf']=get_upload_path($v['swf']);
            
            $giftinfo[$k]=$v;
        }
		$this->assign("giftinfo",$giftinfo);
		$giftinfoj=array();
		foreach($giftinfo as $k=>$v){
			$giftinfoj[$v['id']]=$v;
		}
		$this->assign("giftinfoj",json_encode($giftinfoj));

		$configpri=getConfigPri();

		/* 判断 播流还是推流 */
		$isplay=0;

		if($uid==$anchorinfo['id']){ //推流

			$liveinfo['anyway']=1;

			$checkToken=checkToken($uid,$token);
			if($checkToken==700){
				$this->assign('jumpUrl',__APP__);
				$this->error('登陆过期，请重新登陆');
			} 
			if($configpri['auth_islimit']==1){
				$auth=M("users_auth")->field("status")->where("uid='{$uid}'")->find();
				if(!$auth || $auth['status']!=1)
				{
					$this->assign('jumpUrl',__APP__);
					$this->error("请先进行身份认证");
				}
			}	
			if($configpri['level_islimit']==1){
				if($anchorinfo['level']<$configpri['level_limit'])
				{
					$this->assign('jumpUrl',__APP__);
					$this->error('等级小于'.$configpri['level_limit'].'级，不能直播');
				}						
			}
			$token=getUserToken($uid);
			$this->assign('token',$token);
			/* 流地址 */	
            if($this->configpri['cdn_switch']==5){
                $wyinfo=PrivateKeyA('http',$stream,1);
                $push=$wyinfo['ret']['pushUrl'];
                $wy_cid=$wyinfo['ret']['cid'];
            }else{
                $push=PrivateKeyA('rtmp',$stream,1);
            }
			
			 /*if($uid=8290){
				$push=array(
					'stream'=>'stream',
					'cdn'=>'rtmp://5761.lsspublish.aodianyun.com/1198',
				);
			}*/
			$this->assign('push',$push);
            $this->assign('wy_cid',$wy_cid);
			//$this->display('player');
			$isplay=1;
		}
		 /*else
		{

			$this->display("index");
		}*/ 

		$this->assign('isplay',$isplay);
		
		if($liveinfo['anyway']==1){
			$this->display("index");
		}else{
			$this->display("indexApp");
		}

		//$this->display();
		
  }
	/*
	二维码=====
	value 二维码连接地址
	*/
	function qrcode(){
		$roomid=(int)I("roomid");
		include 'simplewind/Lib/Util/phpqrcode.php'; 
		$a= new \QRcode();
		$value = get_upload_path('/wxshare/index.php/Share/show?roomnum='.$roomid);
		$errorCorrectionLevel = 'L';//容错级别 
		$matrixPointSize = 6;//生成图片大小 
		//生成二维码图片 
		$a->png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2); 
		$logo = 'jb51.png';//准备好的logo图片 
		$QR = 'qrcode.png';//已经生成的原始二维码图 
		if ($logo !== FALSE) { 
			$QR = imagecreatefromstring(file_get_contents($QR)); 
			$logo = imagecreatefromstring(file_get_contents($logo)); 
			$QR_width = imagesx($QR);//二维码图片宽度 
			$QR_height = imagesy($QR);//二维码图片高度 
			$logo_width = imagesx($logo);//logo图片宽度 
			$logo_height = imagesy($logo);//logo图片高度 
			$logo_qr_width = $QR_width / 5; 
			$scale = $logo_width/$logo_qr_width; 
			$logo_qr_height = $logo_height/$scale; 
			$from_width = ($QR_width - $logo_qr_width) / 2; 
			//重新组合图片并调整大小 
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
			$logo_qr_height, $logo_width, $logo_height); 
		} 
		//输出图片 
		Header("Content-type: image/png");
		ImagePng($QR);
	}	
		/* 用户进入 写缓存 
		50本房间主播 60超管 40管理员 30观众 10为游客(判断当前用户身份) 
		*/
	public function setNodeInfo() {
		/* 当前用户信息 */
		$uid=(int)session("uid");
		$showid=(int)I('showid');
		$token=session("token");
		$stream=I('stream');
		if($uid>0){
			$info=getUserInfo($uid);	
			$info['liveuid']=$showid;
			$info['token']=$token;
            
            $stream2=explode('_',$stream);
            $showid2=$stream2[1];
            
            $contribution='0';
            if($showid2){
                $contribution=getContribut($uid,$showid,$showid2);
            }
			$info['contribution']=$contribution;

			$carinfo=getUserCar($uid);
			$info['car']=$carinfo;
            $info['usertype']=getIsAdmin($uid,$showid);
            
            $guard_type=getUserGuard($uid,$showid);
            $info['guard_type']=$guard_type['type'];
            
            /* 等级+100 保证等级位置位数相同，最后拼接1 防止末尾出现0 */
            $info['sign']=$info['contribution'].'.'.($info['level']+100).'1';

		}else{
			/* 游客 */
			$sign= mt_rand(1000,9999);
			$info['id'] = '-'.$sign;
			$info['user_nicename'] = '游客'.$sign;
			$info['avatar'] = '';
			$info['avatar_thumb'] = '';
			$info['sex'] = '0';
			$info['signature'] = '0';
			$info['consumption'] = '0';
			$info['votestotal'] = '0';
			$info['province'] = '';
			$info['city'] = '';
			$info['level'] = '0';
			$info['token']=md5($showid.'_'.$sign);
			$info['liveuid']=$showid;
			$info['usertype']=30;
			$info['contribution']='0';
			$info['guard_type']='0';
			$info['vip']=array('type'=>'0');
            $info['laing']=array('name'=>'0');
			$info['car']=array(
							'id'=>'0',
							'swf'=>'',
							'swftime'=>'0',
							'words'=>'',
						);
            /* 等级+100 保证等级位置位数相同，最后拼接1 防止末尾出现0 */
            $info['sign']=$info['contribution'].'.'.($info['level']+100).'1';
			$token =$info['token'];
		}	
		$info['roomnum']=$showid;
		//判断该房间是否在直播
        $where['islive']=1;
        $where['uid']=$showid;
		$live=M("users_live")->where($where)->find();
		if($live)
		{
			$info['stream']=$live['stream'];
		}
		else
		{
			if($uid==$showid)
			{
				$info['stream']=$stream;
			}
			else
			{
				$info['stream']=$showid."_".$showid;
			}
		}
		$redis = connectionRedis();
		$redis  -> set($token,json_encode($info));
		$redis -> close();	
		/*判断改房间是否开启僵尸粉*/
		$iszombie=isZombie($showid);
		$data=array(
			'error'=>0,
			'userinfo'=>$info,
			'iszombie'=>$iszombie,
		);
		echo  json_encode($data);	
		exit;
    }
	//开播设置
	public function createRoom(){

		$token=I("token");
		$stream=I("stream");
		$uid=(int)I("uid");
		$title=I("title");
		$type=I("type");
		$type_val=I("stand");
        $wy_cid=I("wy_cid");
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        
		$User=M("users");
		$live=M("users_live");
        
        $where['id']=$uid;
        
		$userinfo= $User->field('coin,token,expiretime,ishot,isrecommend')->where($where)->find();
        
		if($userinfo['token']!=session("token") || $userinfo['expiretime']<time())
		{
			echo '{"state":"1002","data":"","msg":"您的登陆状态失效，请重新登陆！"}';
			exit;	
		}
		else
		{
			$configpri=getConfigPri();
			$orderid=explode("_",$stream);
			$showid=$orderid[1];
			
			$liang=getUserLiang($uid);
			$goodnum=0;
			if($liang['name']!=0){
				$goodnum=$liang['name'];
			}
			
			if($configpri['cdn_switch']==5)
			{
				$wyinfo=PrivateKeyA('rtmp',$wy_cid,0);
				$pull=$wyinfo['ret']["rtmpPullUrl"];
			}else{
				$pull=PrivateKeyA('rtmp',$stream,0);
			}
	
			
			$data=array(
				"ishot"=>$userinfo['ishot'],
				"isrecommend"=>$userinfo['isrecommend'],
				"showid"=>$showid,
				"islive"=>"1",
				"starttime"=>$showid,
				"title"=>$title,
				"province"=>"",
				"city"=>"好像在火星",
				"stream"=>$stream,
				"pull"=>$pull,
				"lng"=>"",
				"lat"=>"",
				"wy_cid"=>$wy_cid,
				"goodnum"=>$goodnum,
				"type"=>$type,
				"type_val"=>$type_val,
				"hotvotes"=>0,
				"isvideo"=>0,
				"anyway"=>1
			);
			$users_live=$live->field("uid")->where('uid='.$uid)->find();
			if($users_live){
				/* 更新 */
				$rs=$live->where('uid='.$uid)->save($data);
			}else{
				/*新增*/
				$data['uid']=$uid;
				$rs=$live->add($data);
			}
			
			if($rs)
			{
				$result['city']="好像在火星";
				$redis = connectionRedis();
				$redis  -> set($token,json_encode($result));
				$redis -> close();
				echo '{"state":"0","data":"'.$type.'","msg":""}';
			}
			else
			{
				echo '{"state":"1000","data":"","msg":"直播信息处理失败"}';
			}
			
		}
		exit;
	}
    
    
    /* 关播 */
    public function stopRoom(){
        
        $rs=array('code'=>0,'msg'=>'关播成功','info'=>array());
        $uid=(int)session("uid");
        $stream=I('stream');
        
        if(!$uid || !$stream){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            echo json_encode($rs);
            exit;
        }
        
        $where['islive']=1;
        $where['uid']=$uid;
        $where['stream']=$stream;

		$info=M('users_live')
				->field("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid")
				->where($where)
				->find();
		if($info){
			M("users_live")->where("uid={$uid}")->delete();
			$nowtime=time();
			$info['endtime']=$nowtime;
			$info['time']=date("Y-m-d",$info['showid']);
			$votes=M("users_coinrecord")
				->where("touid={$uid} and showid={$info['showid']}")
				->sum('totalcoin');
			$info['votes']=0;
			if($votes){
				$info['votes']=$votes;
			}
            
            $redis=connectionRedis();
            
			$nums=$redis->zSize('user_'.$stream);			
			$redis->hDel("livelist",$uid);
			$redis->delete($uid.'_zombie');
			$redis->delete($uid.'_zombie_uid');
			$redis->delete('attention_'.$uid);
			$redis->delete('user_'.$stream);
			$info['nums']=$nums;			
			$result=M('users_liverecord')->add($info);	

		}
        
        echo json_encode($rs);
        exit;
    }
    
	/*
	用户列表弹出信息
	uid_admin 50本房间主播 60超管 40管理员 30观众 10为游客(判断当前用户身份) 
	touid_admin 50本房间主播 60超管 40管理员 30观众 10为游客(判断当前点击用户身份)
	*/
	public function popupInfo()
	{
		$uid=(int)session("uid");
		$touid=(int)I('touid');
		$roomid=(int)I('roomid');
		$users=M("Users");
        
        $where['id']=$touid;
        
		$info=$users->field("id,avatar,avatar_thumb,user_nicename")->where($where)->find();
		if($uid>0 &&$uid!=null)
		{
			$uid_admin=getIsAdmin($uid,$roomid);
			$isBlack=isBlack($uid,$touid);
		}
		else
		{
			$uid_admin=10;
			$isBlack="";
		}
		if($touid>0 &&$touid!=null)
		{
			$touid_admin=getIsAdmin($touid,$roomid);
		}
		else
		{
			$touid_admin=10;
		}
		
		$popupInfo=array(
			"state"=>"0",
			"uid_admin"=>$uid_admin,
			"touid_admin"=>$touid_admin,
			"info"=>$info,
			"isBlack"=>$isBlack
		);
		$popupInfo=json_encode($popupInfo);
		echo $popupInfo;
		exit;
	}
	
	/* 直播信息 */
	public function live()
	{
		$uid=(int)I('uid');
        
        $where['islive']=1;
        $where['uid']=$uid;
        
		$liveinfo=M("users_live")->where($where)->find();
		$data=array(
			'error'=>0,
			'data'=>$liveinfo,
			'msg'=>'',
		);
		echo json_encode($data);
		exit;
	}
	/* 排行榜 */
	public function rank(){
		$touid=(int)I('touid');
		$showid=(int)I('showid');
		$list=array();

		if(!$touid){
			echo json_encode($list);
			exit;
		}
		$Coinrecord=M('users_coinrecord');
		//本房间魅力榜
        $where['type']='expend';
        $where['touid']=$touid;
        
		$now=$Coinrecord->field("uid,sum(totalcoin) as total")->where($where)->group("uid")->order("total desc")->limit(0,10)->select();
		foreach($now as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$now[$k]['userinfo']=$userinfo;
			$now[$k]['total']=NumberFormat($v['total']);
		}	
		$list['now']=$now;
		//全站魅力榜
		$all=$Coinrecord->field("uid,sum(totalcoin) as total")->where("type='expend'")->group("uid")->order("total desc")->limit(0,10)->select();
		foreach($all as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$all[$k]['userinfo']=$userinfo;
			$all[$k]['total']=NumberFormat($v['total']);
		}	
		$list['all']=$all;		
		echo json_encode($list);
		exit;
	}
	/*进入直播间检查房间类型*/
	public function checkLive()
	{
		$configpub=getConfigPub();
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=(int)session("uid");
		$liveuid=(int)I("liveuid");
		$stream=I("stream");
		$rs['land']=0;
        
        $where['uid']=$liveuid;
        $where['stream']=$stream;
        
		$islive=M('users_live')->field("islive,type,type_val,starttime")->where($where)->find();
		if($islive['type']==2)
		{
			if($uid>0){
				$rs['land']=0;
			}else{
				$rs['land']=1;
				$rs['type']=$islive['type'];
				$rs['type_msg']='当前房间为付费房间，请先登陆';
				echo json_encode($rs);
				exit;
			}
		}
		if(!$islive ||$islive['islive']==0)
		{
			$rs['code'] = 1005;
			$rs['land']=1;
			$rs['msg'] = '直播已结束，请刷新';
			echo json_encode($rs);
			exit;
		}

			$rs['type']=$islive['type'];
			$rs['type_msg']='';
			$rs['type_value']=$islive['type_val'];
			if($uid>0){
				$userinfo=M("users")
						->field("issuper")
						->where("id={$uid}")
						->find();
				if($userinfo && $userinfo['issuper']==1){
					
					if($islive['type']==6){
						
						$rs['code'] = 1007;
						$rs['land']=1;
						$rs['msg'] = '超管不能进入1v1房间';
						echo json_encode($rs);
						exit;
					}
					$rs['type']='0';
					$rs['type_val']='0';
					$rs['type_msg']='';
					
					echo json_encode($rs);
					exit;
				}
			}

			
			if($islive['type']==1){
				$rs['type_msg']='本房间为密码房间，请输入密码';
				$rs['type_value']=md5($islive['type_val']);
			}
			else if($islive['type']==2)
			{
                $where2['uid']=$uid;
                $where2['touid']=$liveuid;
                $where2['showid']=$islive['starttime'];
                $where2['action']='roomcharge';
                $where2['type']='expend';
				$rs['type_msg']='本房间为收费房间，需支付'.$islive['type_val'].$configpub['name_coin'];
				$isexist=M("users_coinrecord")->field('id')->where($where2)->find();
				if($isexist)
				{
					$rs['type']='0';
					$rs['type_msg']='';
				}
			}
			else if($islive['type']==3)
			{
				$rs['type_msg']='本房间为计时房间，每分钟支付需支付'.$islive['type_val'].$configpub['name_coin'];
			}

		echo  json_encode($rs);
		exit;
	}
	/* 直播/回放 结束后推荐 */
	public function endRecommend(){
		/* 推荐列表 */
			
		$list=M("users_live")->where("islive='1'")->order("rand()")->limit(0,4)->select();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$data=array(
				'error'=>0,
				'data'=>$list,
		 );
		echo  json_encode($data);
		exit;
	}
	/* 关注 */
	public function attention(){
		$uid=(int)session("uid");
		if($uid <1){
			$data=array(
				'error'=> 1,
				'msg'=> "请登录",
				'data'=> "请登录",
			);	
			
		}else{
			$anchorid=(int)I("roomnum");
			if($uid == $anchorid){
				$data=array(
					'error'=> 1,
					'msg'=> "不能关注自己",
					'data'=> "不能关注自己"
				);						
			}else{
				$add=array(
					'uid'	=>	$uid,
					'touid'	=>	$anchorid
				);			
				if(isAttention($uid,$anchorid)){
					$check=M('users_attention')->where($add)->delete();
					if($check !== false){
						$data=array(
							'error'=> 0,
							'msg'=> "+关注",
							'data'=> getFansnums($anchorid)
						);	
					}else{
						$data=array(
							'error'=> 1,
							'data'=> '',
							'msg'=> "取消关注失败",
						);					
					}
				}else{
					$check=M('users_attention')->add($add);
					$black=M('users_black')->where($add)->delete();
					if($check !== false){
						$data=array(
							'error'=> 0,
							'msg'=> "已关注",
							'data'=> getFansnums($anchorid)
						);
					}else{
						$data=array(
							'error'=> 1,
							'msg'=> "关注失败",
							'data'=> "",
						);					
					}	
				}
			}
		}
		echo  json_encode($data);
		exit;
	}
	/*主播页面特殊直播弹窗*/
	public function selectplay()
	{
		$config=getConfigPub();
		$this->assign("config",$config);
		$this->display();
	}
	
	/*切换费用弹窗*/
	public function selectplay2()
	{
		$config=getConfigPub();
		$this->assign("config",$config);
		$this->display();
	}
	public function getUserList(){
		$showid=(int)I("showid");
		$p=I("p");

        $where['islive']=1;
        $where['uid']=$showid;
		$live=M("users_live")->where($where)->find();
		if($live){
			$stream=$live['stream'];
		}else
		{
			$stream=$showid."_".$showid;
		}
		
		$redis = connectionRedis();


		$nums=$redis->zSize('user_'.$stream);
        
        if(!$p){
            $p=1;
        }
		$pnum=20;
		$start=($p-1)*$pnum;
        
		$key="getUserLists_".$showid.'_'.$p;
		$list=getcaches($key);
		
		if(!$list){ 
            $list=array();

            $uidlist=$redis -> zRevRange('user_'.$stream,$start,$pnum,true);
            
            foreach($uidlist as $k=>$v){
                $userinfo=getUserInfo($k);
                $info=explode(".",$v);
                $userinfo['contribution']=(string)$info[0];
                $list[]=$userinfo;
            }
            
            if($list){
                setcaches($key,$list,50);
            }
		}
        
        if(!$list){
            $list=array();
        }


		$redis -> close();
		$data['list']=$list;
		$data['nums']=$nums;
		echo  json_encode($data);	
		exit;
	} 	

	/* 切换直播类型 */
	public function changeTypeValue() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=(int)session("uid");
		$type=I("type");
		$type_val=I("type_value");
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }

		$result['type_value']=$type_val;
		$result['type']=(string)$type;
		$data=array(
			'type'=>$type,
			'type_val'=>$type_val,
		);
		$Live=M('users_live');
		$liveinfo=$Live->field('type')->where("uid={$uid} and islive=1")->find();
		if(!$liveinfo){
			$rs['code'] = 1001;
			$rs['msg'] = '直播信息不存在';
			echo json_encode($rs);
			exit;
		}

		$result2=$Live->where("uid={$uid}")->save($data);
		if($result2===false){
			$rs['code'] = 1003;
			$rs['msg'] = '开启收费房间失败，请重试';
			echo json_encode($rs);
			exit;
		}

		$rs['info']=$result;
		echo json_encode($rs);
		exit;
	}			
}



