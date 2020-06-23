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
 * 消费相关
 */
class SpendController extends HomebaseController {
	/* 送礼物 */
	public function sendGift(){
		$User=M("users");
		$uid=(int)session("uid");
		$touid=(int)I('touid');
		$giftid=(int)I('giftid');
		$giftcount=1;
        if($uid<1){
            echo '{"errno":"1000","data":"","msg":"您的登陆状态失效，请重新登陆！"}';
			exit;	
        }
        if($touid<1){
            echo '{"errno":"1000","data":"","msg":"参数错误"}';
			exit;	
        }
		if($uid==$touid)
		{
			echo '{"errno":"1000","data":"","msg":"不允许给自己送礼物"}';
			exit;	
		}
        $where['id']=$uid;
		$userinfo= $User->field('coin,token,expiretime,user_nicename,avatar')->where($where)->find();
		if($userinfo['token']!=session("token") || $userinfo['expiretime']<time()){
            session('uid',null);		
            session('token',null);
            session('user',null);
            cookie('uid',null);
            cookie('token',null);
			echo '{"errno":"700","data":"","msg":"您的登陆状态失效，请重新登陆！"}';
			exit;	
		} 		
		/* 礼物信息 */
        $where2['id']=$giftid;
		$giftinfo=M("gift")->field("giftname,gifticon,needcoin,type,mark,swftype,swf,swftime")->where($where2)->find();		
		$total= $giftinfo['needcoin']*$giftcount;
		$addtime=time();
        
        if($giftinfo['mark']==2){
            /* 守护 */
            $guard_info=getUserGuard($uid,$touid);
            if($guard_info['type']!=1){
                echo '{"errno":"1002","data":"","msg":"该礼物是守护专属礼物奥~"}';
                exit;	
            }
        }

        $where3['uid']=$touid;
		$users_live=M("users_live")->where("islive=1")->where($where3)->find();
		$showid=0;
		if($users_live){
			$showid=$users_live['starttime'];
		}
		/* 更新用户余额 消费 */
		$ifok=M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}' and coin >={$total}");
		if(!$ifok){
            /* 余额不足 */
			echo '{"errno":"1001","data":"","msg":"余额不足"}';
			exit;	
        }
		/* 分销 */	
		setAgentProfit($uid,$total);
		/* 分销 */	

        $anthor_total=$total;
        /* 幸运礼物分成 */
        if($giftinfo['type']==0 && $giftinfo['mark']==3){
            $jackpotset=getJackpotSet();
            
            $anthor_total=floor($anthor_total*$jackpotset['luck_anchor']*0.01);
        }
        
        /* 幸运礼物分成 */
        
		/* 家族分成之后的金额 */
		$anthor_total=setFamilyDivide($touid,$anthor_total);
		
		/* 更新直播 映票 累计映票 */						 
		M()->execute("update __PREFIX__users set votes=votes+{$anthor_total},votestotal=votestotal+{$total} where id='{$touid}'");
        
        if($anthor_total){
            $insert_votes=[
                'type'=>'income',
                'action'=>'sendgift',
                'uid'=>$touid,
                'votes'=>$anthor_total,
                'addtime'=>time(),
            ];
            M('users_voterecord')->add($insert_votes); 
        }
        
		/* 更新直播 映票 累计映票 */
		M("users_coinrecord")->add(array("type"=>'expend',"action"=>'sendgift',"uid"=>$uid,"touid"=>$touid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"mark"=>$giftinfo['mark'],"addtime"=>$addtime ));
        
        /* 更新主播热门 */
        if($giftinfo['mark']==1){
            M()->execute("update __PREFIX__users_live set hotvotes=hotvotes+{$total} where uid='{$touid}'");
        }
        
        $redis = connectionRedis();
        
        /* PK处理 */
        $key1='LivePK';
        $key2='LivePK_gift';
        
        $ispk='0';
        $pkuid1='0';
        $pkuid2='0';
        $pktotal1='0';
        $pktotal2='0';
        $liveuid=$touid;
        $pkuid=$redis -> hGet($key1,$liveuid);
        if($pkuid){
            $ispk='1';
            $redis -> hIncrBy($key2,$liveuid,$total);
            
            $gift_uid=$redis -> hGet($key2,$liveuid);
            $gift_pkuid=$redis -> hGet($key2,$pkuid);
            
            $pktotal1=$gift_uid;
            $pktotal2=$gift_pkuid;
            
            $pkuid1=$liveuid;
            $pkuid2=$pkuid;
            
        }
        
        
		/* 清除缓存 */
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$touid); 
        
        $userinfo3=$User->field("votestotal")->where("id='{$touid}'")->find();
        
		$gifttoken=md5(md5('sendGift'.$uid.$touid.$giftid.$giftcount.$total.$showid.$addtime));
        
        $swf=$giftinfo['swf'] ? get_upload_path($giftinfo['swf']):'';
        
        
        $ifluck=0;
        $ifup=0;
        $ifwin=0;
        /* 幸运礼物 */
        if($giftinfo['type']==0 && $giftinfo['mark']==3){
            $ifup=1;
            $ifwin=1;
            $list=getLuckRate();
            /* 有中奖配置 才处理 */
            if($list){
                $rateinfo=[];
                foreach($list as $k=>$v){
                    if($v['giftid']==$giftid && $v['nums']==$giftcount){
                        $rateinfo[]=$v;
                    }
                }
                /* 有该礼物、该数量 中奖配置 才处理 */
                if($rateinfo){
                    $ifluck=1;
                }
            }
            
        }
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifluck:'.json_encode($ifluck)."\r\n",FILE_APPEND);
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifwin:'.json_encode($ifwin)."\r\n",FILE_APPEND);
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifup:'.json_encode($ifup)."\r\n",FILE_APPEND);
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rateinfo:'.json_encode($rateinfo)."\r\n",FILE_APPEND);
        /* 幸运礼物中奖 */
        $isluck='0';
        $isluckall='0';
        $luckcoin='0';
        $lucktimes='0';
        if($ifluck ==1 ){
            $luckrate=rand(1,100000);
            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 luckrate:'.json_encode($luckrate)."\r\n",FILE_APPEND);
            $rate=0;
            foreach($rateinfo as $k=>$v){
                $rate+=floor($v['rate']*1000);
                //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rate:'.json_encode($rate)."\r\n",FILE_APPEND);
                if($luckrate <= $rate){
                    /* 中奖 */
                    $isluck='1';
                    $isluckall=$v['isall'];
                    $lucktimes=$v['times'];
                    $luckcoin= $total * $lucktimes;
                    
                    /* 用户加余额  写记录 */
                    M()->execute("update __PREFIX__users set coin=coin+{$luckcoin} where id='{$uid}' ");

                    $insert=array(
                        "type"=>'income',
                        "action"=>'luckgift',
                        "uid"=>$uid,
                        "touid"=>$uid,
                        "giftid"=>$giftid,
                        "giftcount"=>$lucktimes,
                        "totalcoin"=>$luckcoin,
                        "showid"=>$showid,
                        "mark"=>$giftinfo['mark'],
                        "addtime"=>$addtime 
                    );
                    M('users_coinrecord')->add($insert);
                    break;
                }
            }
        }
        
        /* 幸运礼物中奖 */
        
        
        /* 奖池升级 */
        $isup='0';
        $uplevel='0';
        $upcoin='0';
        if($ifup == 1 ){
            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifup:'.json_encode($ifup)."\r\n",FILE_APPEND);
            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotset:'.json_encode($jackpotset)."\r\n",FILE_APPEND);
            if($jackpotset['switch']==1 && $jackpotset['luck_jackpot'] > 0){
                /* 开启奖池 */
                $jackpot_up=floor($total * $jackpotset['luck_jackpot'] * 0.01);
                
                //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_up:'.json_encode($jackpot_up)."\r\n",FILE_APPEND);
                if($jackpot_up){

                    M()->execute("update __PREFIX__jackpot set total=total+{$jackpot_up} where id=1 ");
                    
                    $jackpotinfo=getJackpotInfo();
                    
                    $jackpot_level=getJackpotLevel($jackpotinfo['total']);
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_level:'.json_encode($jackpot_level)."\r\n",FILE_APPEND);
                    if($jackpot_level>$jackpotinfo['level']){
                        $isok=M('jackpot')->where("id = 1 and level < {$jackpot_level}") ->save( array('level' => $jackpot_level ));
                        
                        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 isok:'.json_encode($isok)."\r\n",FILE_APPEND);
                        if($isok){
                            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 isup:'.json_encode($isup)."\r\n",FILE_APPEND);
                            $isup='1';
                            $uplevel=$jackpot_level;
                        }
                    }
                }
            }
        }
        /* 奖池升级 */
        
        /* 奖池中奖 */
        $iswin='0';
        $wincoin='0';
        if($ifwin ==1 ){
            if($jackpotset['switch']==1 ){
               /* 奖池开启 */
               $jackpotinfo=getJackpotInfo();
               //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
               if($jackpotinfo['level']>=1){
                    /* 至少达到第一阶段才能中奖 */
                    
                    $list=getJackpotRate();
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 list:'.json_encode($list)."\r\n",FILE_APPEND);
                    /* 有奖池中奖配置 才处理 */
                    if($list){
                        $rateinfo=[];
                        foreach($list as $k=>$v){
                            if($v['giftid']==$giftid && $v['nums']==$giftcount){
                                $rateinfo=$v;
                                break;
                            }
                        }
                        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rateinfo:'.json_encode($rateinfo)."\r\n",FILE_APPEND);
                        /* 有该礼物中奖配置 才处理 */
                        if($rateinfo){
                            $winrate=rand(1,100000);
                            
                            $rate_jackpot=json_decode($rateinfo['rate_jackpot'],true);
                            
                            $rate=floor($rate_jackpot[$jackpotinfo['level']] * 1000);
                            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 winrate:'.json_encode($winrate)."\r\n",FILE_APPEND);
                            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rate:'.json_encode($rate)."\r\n",FILE_APPEND);
                            if($winrate <= $rate){
                                /* 中奖 */
                                $wincoin2=$jackpotinfo['total'];
                                
                                $isok=M()->execute("update __PREFIX__jackpot set total=total-{$wincoin2},level=0 where id=1 and total >= {$wincoin2}");
                                
                                if($isok){
                                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 iswin:'.'1'."\r\n",FILE_APPEND);
                                    $iswin='1';
                                    $wincoin=(string)$wincoin2;
                                    
                                    /* 用户加余额  写记录 */
                                    $isok=M()->execute("update __PREFIX__users set coin=coin+{$wincoin2} where id = {$uid}");
                                    
                                    $insert=array(
                                        "type"=>'income',
                                        "action"=>'jackpotwin',
                                        "uid"=>$uid,
                                        "touid"=>$uid,
                                        "giftid"=>'0',
                                        "giftcount"=>'1',
                                        "totalcoin"=>$wincoin2,
                                        //"showid"=>$showid,
                                        "mark"=>$giftinfo['mark'],
                                        "addtime"=>$addtime 
                                    );
                                    M('users_coinrecord')->add($insert);
                                }
                            }
                        }
                    }
               }
            }
        }
        /* 奖池中奖 */
        
        $userinfo2=$User->field("consumption,coin,votestotal")->where("id='{$uid}'")->find();
		$level=getLevel($userinfo2['consumption']);
        
		$result=array(
            "uid"=>(int)$uid,
            "giftid"=>(int)$giftid,
            "type"=>$giftinfo['type'],
            "giftcount"=>(int)$giftcount,
            "totalcoin"=>$total,
            "giftname"=>$giftinfo['giftname'],
            "gifticon"=>get_upload_path($giftinfo['gifticon']),
            "swftype"=>$giftinfo['swftype'],
            "swftime"=>$giftinfo['swftime'],
            "swf"=>$swf,
            "level"=>$level,
            "coin"=>$userinfo2['coin'],
            "votestotal"=>$userinfo3['votestotal'],
            
            "isluck"=>$isluck,
            "isluckall"=>$isluckall,
            "luckcoin"=>$luckcoin,
            "lucktimes"=>$lucktimes,
            
            "isup"=>$isup,
            "uplevel"=>$uplevel,
            "upcoin"=>$upcoin,
            
            "iswin"=>$iswin,
            "wincoin"=>$wincoin,
            
            "ispk"=>$ispk,
            "pkuid"=>$pkuid,
            "pkuid1"=>$pkuid1,
            "pkuid2"=>$pkuid2,
            "pktotal1"=>$pktotal1,
            "pktotal2"=>$pktotal2,
        );
        
		
		$redis  -> set($gifttoken,json_encode($result));
        if($users_live){
            $redis->zIncrBy('user_'.$users_live['stream'],$total,$uid);
        }
        
		$redis -> close();	

		echo '{"errno":"0","uid":"'.$uid.'","level":"'.$level.'","type":"'.$giftinfo['type'].'","coin":"'.$userinfo2['coin'].'","gifttoken":"'.$gifttoken.'","msg":"赠送成功"}';
		exit;	
			
	}		
	/* 弹幕 */
	public function sendHorn(){
		$rs = array('code' => 0, 'msg' => '', 'info' =>array());
		$users=M("users");
		$uid=(int)session("uid");
		$liveuid=(int)I("liveuid");
		$content=I("content");
		$stream=I("stream");
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        
        if($liveuid<1){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }

		$configpri=getConfigPri();
		$giftid=0;
		$giftcount=1;
		$giftinfo=array(
			"giftname"=>'弹幕',
			"gifticon"=>'',
			"needcoin"=>$configpri['barrage_fee'],
		);		
		
		$total= $giftinfo['needcoin']*$giftcount;
		 
		$addtime=time();
		$type='expend';
		$action='sendbarrage';

		/* 更新用户余额 消费 */
		$ifok=M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}' and coin >={$total}");
        if(!$ifok){
            $rs['code']=1001;
			$rs['msg']='余额不足';
			echo  json_encode($rs);
			exit;
        }
        
        setAgentProfit($uid,$total);
        
		/* 更新直播主播 映票 累计映票 */						 
		M()->execute("update __PREFIX__users set votes=votes+{$total},votestotal=votestotal+{$total} where id='{$liveuid}'");
        
        $insert_votes=[
            'type'=>'income',
            'action'=>$action,
            'uid'=>$liveuid,
            'votes'=>$total,
            'addtime'=>time(),
        ];
        M('users_voterecord')->add($insert_votes);
				
		$stream2=explode('_',$stream);
		$showid=$stream2[1];
        
        if(!$showid){
            $showid=0;
        }

		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
		$isup=M("users_coinrecord")->add($insert);
					 
		$userinfo2 =$users->field('consumption,coin')->where("id=".$uid)->find();	
		/*获取当前用户的等级*/
		$level=getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$liveuid); 
		/*获取主播影票*/
		$votestotal=$users->field('votestotal,coin')->where("id=".$liveuid)->find();
		
		$barragetoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
		 
		$result=array("uid"=>$uid,"content"=>$content,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$votestotal['votestotal'],"barragetoken"=>$barragetoken);
		$rs['info']=$result;
		/*写入 redis*/
		unset($result['barragetoken']);
		$redis =connectionRedis();
		$redis -> set($barragetoken,json_encode($result));
		$redis -> close();
		
		echo json_encode($rs);
	
	}
	/*设置 取消 管理员*/
	public function cancel()
	{
		$rs = array('code' => 0, 'msg' => '', 'info' =>'操作成功');
		$uid=session("uid");
		$touid=(int)I("touid");
		$showid=(int)I("roomid");
		$users_livemanager=M("users_livemanager");
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        
        if($touid<1){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }
        
		if($uid!=$showid)
		{
			$rs['code']=1001;
			$rs['msg']='不是该房间主播';
			echo  json_encode($rs);
			exit;
		}
		if($uid==$touid)
		{
			$rs['code']=1002;
			$rs['msg']='自己无法管理自己';
			echo  json_encode($rs);
			exit;
		}
		$admininfo=$users_livemanager->where("uid='{$touid}' and liveuid='{$showid}'")->find();
		$rs=M("users")->field("id,avatar,avatar_thumb,user_nicename")->where("id=".$touid)->find();
		if($admininfo)
		{
			$users_livemanager->where("uid='{$touid}' and liveuid='{$showid}'")->delete();
			$rs['isadmin']=0;	
		}
		else
		{
			$count =$users_livemanager->where("liveuid='{$showid}'")->count();
			if($count>=5)
			{
				$rs['code']=1004;
				$rs['msg']='最多设置5个管理员';
				echo  json_encode($rs);
				exit;
			}
			$users_livemanager->add( array("uid"=>$touid,"liveuid"=>$showid));
			$rs['isadmin']=1;
		}
		$rs['msg']="设置成功";
		echo  json_encode($rs);
		exit;
	}
	/*禁言*/
	public function gag()
	{
		$rs = array('code' => 0, 'msg' => '禁言成功', 'info' => array());
		$uid=(int)session("uid");
		$touid=(int)I("touid");
		$liveuid=(int)I("roomid");
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        if($touid<1 || $liveuid<1){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }
        
		$uidtype = getIsAdmin($uid,$liveuid);
		if($uidtype==30 ){
			$rs["code"]=1001;
			$rs["msg"]='你不是主播或者管理员';
			echo  json_encode($rs);
			exit;
		}
		$touidtype = getIsAdmin($touid,$liveuid);
        
        if($touidtype==60 )
		{
			$rs["code"]=1002;
			$rs["msg"]='对方是超管，不能禁言';
			echo  json_encode($rs);
			exit;
		}
        
        if($uidtype==40){
            if($touidtype==50)
            {
                $rs["code"]=1002;
                $rs["msg"]='对方是主播，不能禁言';
                echo  json_encode($rs);
                exit;
            }
            
            if($touidtype==40 )
            {
                $rs["code"]=1002;
                $rs["msg"]='对方是管理员，不能禁言';
                echo  json_encode($rs);
                exit;
            }
            /* 守护 */
            $guard_info=getUserGuard($touid,$liveuid);

            if($uid != $liveuid && $guard_info && $guard_info['type']==2){
                $rs["code"]=1004;
                $rs["msg"]='对方是尊贵守护，不能禁言';
                return $rs;	
            }
        }

		
		$isexist=M('live_shut')
                ->where("uid = {$uid} and liveuid={$liveuid}")
                ->find();
        if($isexist){

            $result=M('live_shut')->where("id={$isexist['id']}")->save([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'showid'=>0,'addtime'=>time() ]);
            
        }else{
            $result=M('live_shut')->add([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'showid'=>0,'addtime'=>time() ]);
        }
        $redis = connectionRedis();
        $redis -> hSet($liveuid . 'shutup',$uid,1);
        $redis -> close();
		echo  json_encode($rs);
		exit;
	}
	public function isShutUp() {
		$uid=(int)session("uid");
		$liveuid=(int)I("showid");
		$rs = array('code' => 0, 'msg' => '', 'info' => '0');
		$nowtime=time();
		if($uid>0 && $liveuid>0)
		{
			$admin = getIsAdmin($uid,$liveuid);
			$rs['admin']=$admin;
			
            $isexist=M('live_shut')
                ->where("uid = {$uid} and liveuid={$liveuid}")
                ->find();
            if($isexist){
                $rs['info']='1';
            }
		
		}
		echo  json_encode($rs);
		exit;
  }
	/*踢人*/
	public function tiren()
	{
		$rs = array('code' => 0, 'msg' => '', 'info' =>'操作成功');
		$uid=(int)session("uid");
		$touid=(int)I("touid");
		$liveuid=(int)I("roomid");
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        if($touid<1 || $liveuid<1){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }
		$uidtype = getIsAdmin($uid,$liveuid);
		if($uidtype==30)
		{
			$rs['code']=1000;
			$rs['msg']='您不是管理员，无权操作';
			echo  json_encode($rs);
			exit;
		}
		$touidtype =getIsAdmin($touid,$liveuid);
        
        if($touidtype==60 )
		{
			$rs["code"]=1002;
			$rs["msg"]='对方是超管，不能被踢出';
			echo  json_encode($rs);
			exit;
		}
        
        if($uidtype==40){
            if($touidtype==50)
            {
                $rs["code"]=1002;
                $rs["msg"]='对方是主播，不能被踢出';
                echo  json_encode($rs);
                exit;
            }
            
            if($touidtype==40 )
            {
                $rs["code"]=1002;
                $rs["msg"]='对方是管理员，不能被踢出';
                echo  json_encode($rs);
                exit;
            }
            /* 守护 */
            $guard_info=getUserGuard($touid,$liveuid);

            if($uid != $liveuid && $guard_info && $guard_info['type']==2){
                $rs["code"]=1004;
                $rs["msg"]='对方是尊贵守护，不能被踢出';
                return $rs;	
            }
        }
        
        $isexist=M('live_kick')
                ->where("uid={$touid} and liveuid={$liveuid} ")
                ->find();
        if($isexist){
            $rs["code"]=1005;
            $rs["msg"]='对方已被踢出';
            echo  json_encode($rs);
            exit;
        }
        
        $result=M('live_kick')->add([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'addtime'=>time() ]);
        
		echo  json_encode($rs);
		exit;	
	}
	/*加入/取消 黑名单*/
	public function black()
	{
		$rs = array('code' => 0, 'msg' => '', 'info' =>'操作成功');
		$uid=(int)session("uid");
		$touid=(int)I("touid");
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        if($touid<1){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }
		if($uid==$touid)
		{
			$rs['code']=0;
			$rs['msg']='无法将自己拉黑';
			echo  json_encode($rs);
			exit;
		}
		$users_black=M(users_black);
        $where['uid']=$uid;
        $where['touid']=$touid;
		$isexist=$users_black->where($where)->find();
		if($isexist)
		{
			$black=$users_black->where($where)->delete();
			if($black)
			{
				$rs['code']=0;
				$rs['msg']='已将该用户移除黑名单';
				echo  json_encode($rs);
				exit;
			}
			else
			{
				$rs['code']=1000;
				$rs['msg']='移除黑名单失败';
				echo  json_encode($rs);
				exit;
			}
		}
		else
		{
			M('users_attention')->where($where)->delete();
			$black=$users_black->add(array("uid"=>$uid,"touid"=>$touid));
			if($black)
			{
				$rs['code']=0;
				$rs['msg']='已将该用户添加到黑名单';
				echo  json_encode($rs);
				exit;
			}
			else
			{
				$rs['code']=1000;
				$rs['msg']='添加黑名单失败';
				echo  json_encode($rs);
				exit;
			}
			
		}			 
	}
	/*举报*/
	public function report()
	{
		$rs = array('code' => 0, 'msg' => '', 'info' =>'操作成功');
		$uid=(int)session("uid");
		$tlleuid=(int)I("tlleuid");
		$token=I("token");
		$liveuid=(int)I("liveuid");
		$content=I("content");
        
        
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        
        if($tlleuid<1 || $liveuid<1){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }

        
		$users=M("users");
		if($uid!=$tlleuid)
		{
			$rs['code']=1000;
			$rs['msg']='未知信息错误';
			echo  json_encode($rs);
			exit;
		}
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			$rs['code']=$checkToken;
			$rs['msg']='登录信息过期，请重新登录';
			echo  json_encode($rs);
			exit;
		}
		if($content=="")
		{
			$rs['code']=1001;
			$rs['msg']='举报内容不能为空';
			echo  json_encode($rs);
			exit;
		}
		$data=array(
			"uid"=>$uid,
			"touid"=>$liveuid,
			'content'=>$content,
			'addtime'=>time() 
		);
		$users_report=M("users_report")->add($data);	
		if($users_report)
		{
			$rs['code']=0;
			$rs['msg']='举报成功';
			echo  json_encode($rs);
			exit;
		}
		else
		{
			$rs['code']=1003;
			$rs['msg']='举报失败';
			echo  json_encode($rs);
			exit;
		}
		 
	}

	/*收费房间扣费*/
	public function roomCharge()
	{
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$liveuid=(int)I("liveuid");
		$stream=I("stream");
		$uid=(int)session("uid");
		$token=session("token");
        if($uid<1){
            $rs['code']=1000;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo  json_encode($rs);
			exit;            
        }
        
        if($liveuid<1 || $stream==''){
            $rs['code']=1000;
			$rs['msg']='参数错误';
			echo  json_encode($rs);
			exit;            
        }
        
        $where['uid']=$liveuid;
        $where['stream']=$stream;
        
		$islive=M("users_live")->field("islive,type,type_val,starttime")->where($where)->find();
		if(!$islive || $islive['islive']==0){
			$rs['code'] = 1005;
			$rs['msg'] = '直播已结束';
			echo json_encode($rs);
			exit;
		}
		if($islive['type']==0 || $islive['type']==1 ){
			$rs['code'] = 1006;
			$rs['msg'] = '该房间非扣费房间';
			echo json_encode($rs);
			exit;
		}
		$userinfo=M("users")->field("token,expiretime,coin")->where("id='{$uid}'")->find();
		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
            session('uid',null);		
            session('token',null);
            session('user',null);
            session('user_nicename',null);
            session('avatar',null);
            cookie('uid',null);
            cookie('token',null);
			$rs['code'] = 700;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;			
		}
		$total=$islive['type_val'];
		if($total<=0){
			$rs['code'] = 1007;
			$rs['msg'] = '房间费用有误';
			echo json_encode($rs);
			exit;
		}
		$action='roomcharge';
		if($islive['type']==3){
			$action='timecharge';
		}
		$giftid=0;
		$giftcount=0;
		$showid=$islive['starttime'];
		$addtime=time();
		/* 更新用户余额 消费 */
		$ifok=M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}' and coin>={$total}");
		if(!$ifok){
            $rs['code'] = 1008;
			$rs['msg'] = '余额不足';
			echo json_encode($rs);
			exit;
        }
		/* 分销 */	
		setAgentProfit($uid,$total);
		/* 分销 */		
	
		/* 家族分成之后的金额 */
		$anthor_total=setFamilyDivide($liveuid,$total);
		
		/* 更新直播 映票 累计映票 */
		M()->execute("update __PREFIX__users set votes=votes+{$anthor_total},votestotal=votestotal+{$total} where id='{$liveuid}'");
        $insert_votes=[
            'type'=>'income',
            'action'=>$action,
            'uid'=>$liveuid,
            'votes'=>$anthor_total,
            'addtime'=>time(),
        ];
        M('users_voterecord')->add($insert_votes);
		/* 更新直播 映票 累计映票 */
		M("users_coinrecord")->add(array("type"=>'expend',"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));		
		$userinfo2=M("users")->field('coin')->where("id='{$uid}'")->find();	
		$rs['coin']=$userinfo2['coin'];
		echo json_encode($rs);
	}


}


