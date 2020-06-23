<?php

class Model_Live extends PhalApi_Model_NotORM {
	/* 创建房间 */
	public function createRoom($uid,$data) {
        
        /* 获取主播 推荐、热门 */
        $data['ishot']='0';
        $data['isrecommend']='0';
        $userinfo=DI()->notorm->users
					->select("ishot,isrecommend")
					->where('id=?',$uid)
					->fetchOne();
        if($userinfo){
            $data['ishot']=$userinfo['ishot'];
            $data['isrecommend']=$userinfo['isrecommend'];
        }
		$isexist=DI()->notorm->users_live
					->select("uid")
					->where('uid=?',$uid)
					->fetchOne();
		if($isexist){
            /* 判断存在的记录是否为直播状态 */
            if($isexist['isvideo']==0 && $isexist['islive']==1){
                /* 若存在未关闭的直播 关闭直播 */
                $this->stopRoom($uid,$isexist['stream']);
                
                /* 加入 */
                $rs=DI()->notorm->users_live->insert($data);
            }else{
                /* 更新 */
                $rs=DI()->notorm->users_live->where('uid = ?', $uid)->update($data);
            }
		}else{
			/* 加入 */
			$rs=DI()->notorm->users_live->insert($data);
		}
		if(!$rs){
			return $rs;
		}
		return 1;
	}
	
	/* 主播粉丝 */
    public function getFansIds($touid) {
        
        $list=array();
		$fansids=DI()->notorm->users_attention
					->select("uid")
					->where('touid=?',$touid)
					->fetchAll();
                    
        if($fansids){
            $uids=array_column($fansids,'uid');
            
            $pushids=DI()->notorm->users_pushid
					->select("pushid")
					->where('uid',$uids)
					->fetchAll();
            $list=array_column($pushids,'pushid');
            $list=array_filter($list);
        }
        return $list;
    }	
	
	/* 修改直播状态 */
	public function changeLive($uid,$stream,$status){

		if($status==1){
            $info=DI()->notorm->users_live
                    ->select("*")
					->where('uid=? and stream=?',$uid,$stream)
                    ->fetchOne();
            if($info){
                DI()->notorm->users_live
					->where('uid=? and stream=?',$uid,$stream)
					->update(array("islive"=>1));
            }
			return $info;
		}else{
			$this->stopRoom($uid,$stream);
			return 1;
		}
	}
	
	/* 修改直播状态 */
	public function changeLiveType($uid,$stream,$data){
		return DI()->notorm->users_live
				->where('uid=? and stream=?',$uid,$stream)
				->update( $data );
	}
	
	/* 关播 */
	public function stopRoom($uid,$stream) {

		$info=DI()->notorm->users_live
				->select("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid")
				->where('uid=? and stream=? and islive="1"',$uid,$stream)
				->fetchOne();
        file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 info:'.json_encode($info)."\r\n",FILE_APPEND);
		if($info){
			$isdel=DI()->notorm->users_live
				->where('uid=?',$uid)
				->delete();
            if(!$isdel){
                return 0;
            }
			$nowtime=time();
			$info['endtime']=$nowtime;
			$info['time']=date("Y-m-d",$info['showid']);
			$votes=DI()->notorm->users_coinrecord
				->where('uid !=? and touid=? and showid=?',$uid,$uid,$info['showid'])
				->sum('totalcoin');
			$info['votes']=0;
			if($votes){
				$info['votes']=$votes;
			}
			$nums=DI()->redis->zCard('user_'.$stream);			
			DI()->redis->hDel("livelist",$uid);
			DI()->redis->delete($uid.'_zombie');
			DI()->redis->delete($uid.'_zombie_uid');
			DI()->redis->delete('attention_'.$uid);
			DI()->redis->delete('user_'.$stream);
			$info['nums']=$nums;			
			$result=DI()->notorm->users_liverecord->insert($info);	
            file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result['id'])."\r\n",FILE_APPEND);
            
            /* 解除本场禁言 */
            $list2=DI()->notorm->live_shut
                ->select('uid')
                ->where('liveuid=? and showid!=0',$uid)
                ->fetchAll();
            DI()->notorm->live_shut->where('liveuid=? and showid!=0',$uid)->delete();
            
            foreach($list2 as $k=>$v){
                DI()->redis -> hDel($uid . 'shutup',$v['uid']);
            }
		}
		return 1;
	}
	/* 关播信息 */
	public function stopInfo($stream){
		
		$rs=array(
			'nums'=>0,
			'length'=>0,
			'votes'=>0,
		);
		
		$stream2=explode('_',$stream);
		$liveuid=$stream2[0];
		$starttime=$stream2[1];
		$liveinfo=DI()->notorm->users_liverecord
					->select("starttime,endtime,nums,votes")
					->where('uid=? and starttime=?',$liveuid,$starttime)
					->fetchOne();
		if($liveinfo){
            $cha=$liveinfo['endtime'] - $liveinfo['starttime'];
			$rs['length']=getSeconds($cha,1);
			$rs['nums']=$liveinfo['nums'];
		}
		if($liveinfo['votes']){
			$rs['votes']=$liveinfo['votes'];
		}
		return $rs;
	}
	
	/* 直播状态 */
	public function checkLive($uid,$liveuid,$stream){
        
        /* 是否被踢出 */
        $isexist=DI()->notorm->live_kick
					->select("id")
					->where('uid=? and liveuid=?',$uid,$liveuid)
					->fetchOne();
        if($isexist){
            return 1008;
        }
        
		$islive=DI()->notorm->users_live
					->select("islive,type,type_val,starttime")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();
					
		if(!$islive || $islive['islive']==0){
			return 1005;
		}
		$rs['type']=$islive['type'];
		$rs['type_val']='0';
		$rs['type_msg']='';
		
			$userinfo=DI()->notorm->users
					->select("issuper")
					->where('id=?',$uid)
					->fetchOne();
			if($userinfo && $userinfo['issuper']==1){
                
                if($islive['type']==6){
                    
                    return 1007;
                }
				$rs['type']='0';
				$rs['type_val']='0';
				$rs['type_msg']='';
				
				return $rs;
			}
		
		if($islive['type']==1){
			$rs['type_msg']=md5($islive['type_val']);
		}else if($islive['type']==2){
			$rs['type_msg']='本房间为收费房间，需支付'.$islive['type_val'].'钻石';
			$rs['type_val']=$islive['type_val'];
			$isexist=DI()->notorm->users_coinrecord
						->select('id')
						->where('uid=? and touid=? and showid=? and action="roomcharge" and type="expend"',$uid,$liveuid,$islive['starttime'])
						->fetchOne();
			if($isexist){
				$rs['type']='0';
				$rs['type_val']='0';
				$rs['type_msg']='';
			}
		}else if($islive['type']==3){
			$rs['type_val']=$islive['type_val'];
			$rs['type_msg']='本房间为计时房间，每分钟需支付'.$islive['type_val'].'钻石';
		}
		
		return $rs;
		
	}
	
	/* 用户余额 */
	public function getUserCoin($uid){
		$userinfo=DI()->notorm->users
					->select("coin")
					->where('id=?',$uid)
					->fetchOne();
		return $userinfo;
	}
	
	/* 房间扣费 */
	public function roomCharge($uid,$token,$liveuid,$stream){
		$islive=DI()->notorm->users_live
					->select("islive,type,type_val,starttime")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();
		if(!$islive || $islive['islive']==0){
			return 1005;
		}
		
		if($islive['type']==0 || $islive['type']==1 ){
			return 1006;
		}
		
		$userinfo=DI()->notorm->users
					->select("token,expiretime,coin")
					->where('id=?',$uid)
					->fetchOne();
		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
			return 700;				
		}
        
        
		
		$total=$islive['type_val'];
		if($total<=0){
			return 1007;
		}
        
        /* 更新用户余额 消费 */
		$ifok=DI()->notorm->users
				->where('id = ? and coin >= ?', $uid,$total)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}")) );
        if(!$ifok){
            return 1008;
        }

		$action='roomcharge';
		if($islive['type']==3){
			$action='timecharge';
		}
		
		$giftid=0;
		$giftcount=0;
		$showid=$islive['starttime'];
		$addtime=time();
		
                
        /* 分销 */	
		setAgentProfit($uid,$total);
		/* 分销 */	

		/* 更新直播 映票 累计映票 */
		DI()->notorm->users
				->where('id = ?', $liveuid)
				->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
        
        $insert_votes=[
            'type'=>'income',
            'action'=>$action,
            'uid'=>$liveuid,
            'votes'=>$total,
            'addtime'=>time(),
        ];
        DI()->notorm->users_voterecord->insert($insert_votes);

		/* 更新直播 映票 累计映票 */
		DI()->notorm->users_coinrecord
				->insert(array("type"=>'expend',"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));	
				
		$userinfo2=DI()->notorm->users
					->select('coin')
					->where('id = ?', $uid)
					->fetchOne();	
		$rs['coin']=$userinfo2['coin'];
		return $rs;
		
	}
	
	/* 判断是否僵尸粉 */
	public function isZombie($uid) {
        $userinfo=DI()->notorm->users
					->select("iszombie")
					->where("id='{$uid}'")
					->fetchOne();
		
		return $userinfo['iszombie'];				
    }
	
	/* 僵尸粉 */
    public function getZombie($stream,$where) {
		$ids= DI()->notorm->users_zombie
            ->select('uid')
            ->where("uid not in ({$where})")
			->limit(0,10)
            ->fetchAll();	

		$info=array();

		if($ids){
            foreach($ids as $k=>$v){
                
                $userinfo=getUserInfo($v['uid'],1);
                if(!$userinfo){
                    DI()->notorm->users_zombie->where("uid={$v['uid']}")->delete();
                    continue;
                }
                
                $info[]=$userinfo;

                $score='0.'.($userinfo['level']+100).'1';
				DI()->redis -> zAdd('user_'.$stream,$score,$v['uid']);
            }	
		}
		return 	$info;		
    }
	
	/* 礼物列表 */
	public function getGiftList(){

		$rs=DI()->notorm->gift
			->select("id,type,mark,giftname,needcoin,gifticon")
			->order("orderno asc,addtime desc")
			->fetchAll();

		return $rs;
	}
	
	/* 赠送礼物 */
	public function sendGift($uid,$liveuid,$stream,$giftid,$giftcount,$ispack) {

        /* 礼物信息 */
		$giftinfo=DI()->notorm->gift
					->select("type,mark,giftname,gifticon,needcoin,swftype,swf,swftime")
					->where('id=?',$giftid)
					->fetchOne();
		if(!$giftinfo){
			/* 礼物信息不存在 */
			return 1002;
		}
        
		$total= $giftinfo['needcoin']*$giftcount;
		 
		$addtime=time();
		$type='expend';
		$action='sendgift';
		
        $stream2=explode('_',$stream);
        $showid=$stream2[1];
            
        if($ispack==1){
            /* 背包礼物 */
            $ifok =DI()->notorm->backpack
                    ->where('uid=? and giftid=? and nums>=?',$uid,$giftid,$giftcount)
                ->update(array('nums'=> new NotORM_Literal("nums - {$giftcount} ")));
            if(!$ifok){
                /* 数量不足 */
                return 1003;
            }
        }else{
           /* 更新用户余额 消费 */
            $ifok =DI()->notorm->users
                    ->where('id = ? and coin >=?', $uid,$total)
                    ->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );
            if(!$ifok){
                /* 余额不足 */
                return 1001;
            } 
            
            $insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"mark"=>$giftinfo['mark'],"addtime"=>$addtime );
            DI()->notorm->users_coinrecord->insert($insert);
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
		$anthor_total=setFamilyDivide($liveuid,$anthor_total);

		/* 更新直播 魅力值 累计魅力值 */
		$istouid =DI()->notorm->users
					->where('id = ?', $liveuid)
					->update( array('votes' => new NotORM_Literal("votes + {$anthor_total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
        if($anthor_total){
            $insert_votes=[
                'type'=>'income',
                'action'=>$action,
                'uid'=>$liveuid,
                'votes'=>$anthor_total,
                'addtime'=>time(),
            ];
            DI()->notorm->users_voterecord->insert($insert_votes);
        }

        /* 更新主播热门 */
        if($giftinfo['mark']==1){
            DI()->notorm->users_live
                ->where('uid = ?', $liveuid)
                ->update( array('hotvotes' => new NotORM_Literal("hotvotes + {$total}") ));
        }
        
        DI()->redis->zIncrBy('user_'.$stream,$total,$uid);
        
        /* PK处理 */
        $key1='LivePK';
        $key2='LivePK_gift';
        
        $ispk='0';
        $pkuid1='0';
        $pkuid2='0';
        $pktotal1='0';
        $pktotal2='0';
        
        $pkuid=DI()->redis -> hGet($key1,$liveuid);
        if($pkuid){
            $ispk='1';
            DI()->redis -> hIncrBy($key2,$liveuid,$total);
            
            $gift_uid=DI()->redis -> hGet($key2,$liveuid);
            $gift_pkuid=DI()->redis -> hGet($key2,$pkuid);
            
            $pktotal1=$gift_uid;
            $pktotal2=$gift_pkuid;
            
            $pkuid1=$liveuid;
            $pkuid2=$pkuid;
            
        }
		
        
		/* 清除缓存 */
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$liveuid); 
	
		$votestotal=$this->getVotes($liveuid);
		
		$gifttoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
        
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
                    DI()->notorm->users
                        ->where('id = ?', $uid)
                        ->update( array('coin' => new NotORM_Literal("coin + {$luckcoin}") ));
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
                    DI()->notorm->users_coinrecord->insert($insert);
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
                    DI()->notorm->jackpot->where("id = 1 ") ->update( array('total' => new NotORM_Literal("total + {$jackpot_up}") ));
                    
                    $jackpotinfo=getJackpotInfo();
                    
                    $jackpot_level=getJackpotLevel($jackpotinfo['total']);
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_level:'.json_encode($jackpot_level)."\r\n",FILE_APPEND);
                    if($jackpot_level>$jackpotinfo['level']){
                        $isok=DI()->notorm->jackpot->where("id = 1 and level < {$jackpot_level}") ->update( array('level' => $jackpot_level ));
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
                                $isok=DI()->notorm->jackpot->where("id = 1 and total >= {$wincoin2}") ->update( array('total' => new NotORM_Literal("total - {$wincoin2}"),'level'=>'0' ));
                                if($isok){
                                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 iswin:'.'1'."\r\n",FILE_APPEND);
                                    $iswin='1';
                                    $wincoin=(string)$wincoin2;
                                    
                                    /* 用户加余额  写记录 */
                                    DI()->notorm->users
                                        ->where('id = ?', $uid)
                                        ->update( array('coin' => new NotORM_Literal("coin + {$wincoin2}") ));
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
                                    DI()->notorm->users_coinrecord->insert($insert);
                                }
                            }
                        }
                    }
               }
            }
        }
        /* 奖池中奖 */
        
        
        $userinfo2 =DI()->notorm->users
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$level=getLevel($userinfo2['consumption']);	
		
		$result=array(
            "uid"=>$uid,
            "giftid"=>$giftid,
            "type"=>$giftinfo['type'],
            "mark"=>$giftinfo['mark'],
            "giftcount"=>$giftcount,
            "totalcoin"=>$total,
            "giftname"=>$giftinfo['giftname'],
            "gifticon"=>get_upload_path($giftinfo['gifticon']),
            "swftime"=>$giftinfo['swftime'],
            "swftype"=>$giftinfo['swftype'],
            "swf"=>$swf,
            "level"=>$level,
            "coin"=>$userinfo2['coin'],
            "votestotal"=>$votestotal,
            "gifttoken"=>$gifttoken,
            
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
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
		return $result;
	}		
	
	/* 发送弹幕 */
	public function sendBarrage($uid,$liveuid,$stream,$giftid,$giftcount,$content) {

		$configpri=getConfigPri();
					 
		$giftinfo=array(
			"giftname"=>'弹幕',
			"gifticon"=>'',
			"needcoin"=>$configpri['barrage_fee'],
		);		
		
		$total= $giftinfo['needcoin']*$giftcount;
		if($total<=0){
            return 1002;
        }
        $addtime=time();
        $type='expend';
        $action='sendbarrage';

        /* 更新用户余额 消费 */
        $ifok =DI()->notorm->users
                ->where('id = ? and coin >=?', $uid,$total)
                ->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );
        if(!$ifok){
            /* 余额不足 */
            return 1001;
        }
        
        setAgentProfit($uid,$total);
        
        /* 更新直播 魅力值 累计魅力值 */
        $istouid =DI()->notorm->users
                ->where('id = ?', $liveuid)
                ->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
                
        $insert_votes=[
            'type'=>'income',
            'action'=>$action,
            'uid'=>$liveuid,
            'votes'=>$total,
            'addtime'=>time(),
        ];
        DI()->notorm->users_voterecord->insert($insert_votes);
                
        $stream2=explode('_',$stream);
        $showid=$stream2[1];
        if(!$showid){
            $showid=0;
        }

        /* 写入记录 或更新 */
        $insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
        $isup=DI()->notorm->users_coinrecord->insert($insert);
        
		$userinfo2 =DI()->notorm->users
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$level=getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$liveuid); 
		
		$votestotal=$this->getVotes($liveuid);
		
		$barragetoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
		 
		$result=array("uid"=>$uid,"content"=>$content,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$votestotal,"barragetoken"=>$barragetoken);
		
		return $result;
	}			
	
	/* 设置/取消 管理员 */
	public function setAdmin($liveuid,$touid){
					
		$isexist=DI()->notorm->users_livemanager
					->select("*")
					->where('uid=? and  liveuid=?',$touid,$liveuid)
					->fetchOne();			
		if(!$isexist){
			$count =DI()->notorm->users_livemanager
						->where('liveuid=?',$liveuid)
						->count();	
			if($count>=5){
				return 1004;
			}		
			$rs=DI()->notorm->users_livemanager
					->insert(array("uid"=>$touid,"liveuid"=>$liveuid) );	
			if($rs!==false){
				return 1;
			}else{
				return 1003;
			}				
			
		}else{
			$rs=DI()->notorm->users_livemanager
				->where('uid=? and  liveuid=?',$touid,$liveuid)
				->delete();		
			if($rs!==false){
				return 0;
			}else{
				return 1003;
			}						
		}
	}
	
	/* 管理员列表 */
	public function getAdminList($liveuid){
		$rs=DI()->notorm->users_livemanager
						->select("uid")
						->where('liveuid=?',$liveuid)
						->fetchAll();	
		foreach($rs as $k=>$v){
			$rs[$k]=getUserInfo($v['uid']);
		}	

        $info['list']=$rs;
        $info['nums']=(string)count($rs);
        $info['total']='5';
		return $info;
	}
    
	/* 举报类型 */
	public function getReportClass(){
		return  DI()->notorm->users_report_classify
                    ->select("*")
					->order("orderno asc")
					->fetchAll();
	}
	
	/* 举报 */
	public function setReport($uid,$touid,$content){
		return  DI()->notorm->users_report
				->insert(array("uid"=>$uid,"touid"=>$touid,'content'=>$content,'addtime'=>time() ) );	
	}
	
	/* 主播总映票 */
	public function getVotes($liveuid){
		$userinfo=DI()->notorm->users
					->select("votestotal")
					->where('id=?',$liveuid)
					->fetchOne();	
		return $userinfo['votestotal'];					
	}
    
    /* 是否禁言 */
	public function checkShut($uid,$liveuid){
        
        $isexist=DI()->notorm->live_shut
                ->where('uid=? and liveuid=? ',$uid,$liveuid)
                ->fetchOne();
        if($isexist){
            DI()->redis -> hSet($liveuid . 'shutup',$uid,1);
        }else{
            DI()->redis -> hDel($liveuid . 'shutup',$uid);
        }
		return 1;			
	}

    /* 禁言 */
	public function setShutUp($uid,$liveuid,$touid,$showid){
        
        $isexist=DI()->notorm->live_shut
                ->where('uid=? and liveuid=? ',$touid,$liveuid)
                ->fetchOne();
        if($isexist){
            if($isexist['showid']==$showid){
                return 1002;
            }
            
            
            if($isexist['showid']==0 && $showid!=0){
                return 1002;
            }
            
            $rs=DI()->notorm->live_shut->where('id=?',$isexist['id'])->update([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'showid'=>$showid,'addtime'=>time() ]);
            
        }else{
            $rs=DI()->notorm->live_shut->insert([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'showid'=>$showid,'addtime'=>time() ]);
        }
        
        
        
		return $rs;			
	}
    
    /* 踢人 */
	public function kicking($uid,$liveuid,$touid){
        
        $isexist=DI()->notorm->live_kick
                ->where('uid=? and liveuid=? ',$touid,$liveuid)
                ->fetchOne();
        if($isexist){
            return 1002;
        }
        
        $rs=DI()->notorm->live_kick->insert([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'addtime'=>time() ]);
        
        
		return $rs;
	}
    
    /* 是否禁播 */
	public function checkBan($uid){
        
        $isexist=DI()->notorm->live_ban
                ->where('liveuid=? ',$uid)
                ->fetchOne();
        if($isexist){
            return 1;
        }
		return 0;			
	}    
	
	/* 超管关闭直播间 */
	public function superStopRoom($uid,$token,$liveuid,$type){
		
		$userinfo=DI()->notorm->users
					->select("token,expiretime,issuper")
					->where('id=? ',$uid)
					->fetchOne();
		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
			return 700;				
		} 	
		
		if($userinfo['issuper']==0){
			return 1001;
		}
		
		if($type==1){
			
            /* 禁播列表 */
            $isexist=DI()->notorm->live_ban->where('liveuid=? ',$liveuid)->fetchOne();
            if($isexist){
                return 1002;
            }
            DI()->notorm->live_ban->insert([ 'liveuid'=>$liveuid,'superid'=>$uid,'addtime'=>time() ]);
		}
        
        if($type==2){
            /* 关闭并禁用 */
			DI()->notorm->users->where('id=? ',$liveuid)->update(array('user_status'=>0));
        }
		
	
		$info=DI()->notorm->users_live
				->select("stream")
				->where('uid=? and islive="1"',$liveuid)
				->fetchOne();
		if($info){
            $this->stopRoom($liveuid,$info['stream']);
		}

		
		return 0;
		
	}
    
    /* 获取用户本场贡献 */
    public function getContribut($uid,$liveuid,$showid){
        $sum=DI()->notorm->users_coinrecord
				->where('action="sendgift" and uid=? and touid=? and showid=? ',$uid,$liveuid,$showid)
				->sum('totalcoin');
        if(!$sum){
            $sum=0;
        }
        
        return (string)$sum;
    }

    /* 检测房间状态 */
    public function checkLiveing($uid,$stream){
        $info=DI()->notorm->users_live
                ->select('uid')
				->where('uid=? and stream=? ',$uid,$stream)
				->fetchOne();
        if($info){
            return '1';
        }
        
        return '0';
    }
    
    /* 获取直播信息 */
    public function getLiveInfo($liveuid){
        
        $info=DI()->notorm->users_live
					->select("uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,isshop")
					->where('uid=? and islive=1',$liveuid)
					->fetchOne();
        if($info){
            $configpri=getConfigPri();
            
            $nums=DI()->redis->zCard('user_'.$info['stream']);

			$info['nums']=(string)$nums;
			
            $userinfo=getUserInfo($info['uid']);
			$info['avatar']=$userinfo['avatar'];
			$info['avatar_thumb']=$userinfo['avatar_thumb'];
			$info['user_nicename']=$userinfo['user_nicename'];
			$info['sex']=$userinfo['sex'];
			$info['level']=$userinfo['level'];
			$info['level_anchor']=$userinfo['level_anchor'];

			if(!$info['thumb']){
				$info['thumb']=$info['avatar'];
			}
			if($info['isvideo']==0 && $configpri['cdn_switch']!=5){
				$info['pull']=PrivateKeyA('rtmp',$info['stream'],0);
			}
			
			if($info['type']==1){
				$info['type_val']='';
			}
        }
        
        return $info;
    }        
}
