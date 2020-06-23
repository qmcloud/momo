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
 * 单页
 */
class PageController extends HomebaseController {
	
	public $field='id,user_nicename,avatar,sex,signature,experience,consumption,votestotal,province,city,isrecommend,islive';
    //服务条款
	public function agreement() {
       
			$agreement=M("posts")->where("id='4'")->find();
			
			$this->assign("agreement",$agreement);
			
    	$this->display();
	}	
	// 註冊
	public function registered() {
		$this->assign("registered",$registered);
		$this->display();
	}	
	// 登入
	public function login() {
		$this->assign("login",$login);
		$this->display();
	}	
	// message
	public function msg() {
		$this->assign("msg",$msg);
		$this->display();
	}
	// game
	public function game() {
		$this->assign("game",$game);
		$this->display();
	}
	// game open
	public function gameopen() {
		$this->assign("gameopen",$gameopen);
		$this->display();
	}
	// forget password
	public function forgetpass() {
		$this->assign("forgetpass",$forgetpass);
		$this->display();
	}	
	// 1v1
	public function room1v1() {
		$this->assign("room1v1",$room1v1);
		$this->display();
	}
	// personal
	public function personal() {
		$this->assign("personal",$personal);
		$this->display();
	}	
	public function search() {
		$prefix= C("DB_PREFIX");
		$this->assign("current",'index');	
		$uid=session("uid");
		$firstLive="";

		/*获取推荐播放列表(正在直播，推荐，按粉丝数排序)*/

		$indexLive=M("users_live")->where("islive='1' and isrecommend='1' and type='0'")->select();

		foreach ($indexLive as $k => $v){
            $userinfo=getUserInfo($v['uid']);
            
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
            
			if($v['thumb']==""){
				$v['thumb']=$v['avatar'];
			}
			if($v['isvideo']==0){
                if($this->configpri['cdn_switch']!=5){
                    $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
                }
            }
			$v['fans_nums']=M("users_attention")->where("touid={$v['uid']}")->count();
            $indexLive[$k]=$v;
		}

		$sort=array_column($indexLive,"fans_nums");
		array_multisort($sort, SORT_DESC, $indexLive);
		$indexLive1=array_slice($indexLive,0,4);
		$firstLive=$indexLive[0]['pull'];
		$firstUid=$indexLive[0]['uid'];
		$this->assign("indexLive",$indexLive1);
		$this->assign("firstUid",$firstUid);
		$this->assign("firstLive",$firstLive);
		//var_dump($indexLive1);
		/* 轮播 */
		$slide=M("slide")->where("slide_status='1' and slide_cid='1'")->order("listorder asc")->select();
		$this->assign("slide",$slide);	

		$redis =connectionRedis();

		/* 推荐（正在直播 在线人数） */
		$recommend=M("users_live ")
					->field("uid,thumb,uid,stream,type,islive")
					->where("islive='1'")
					->limit(12)
					->select();
		foreach($recommend as $k=>$v){
            $userinfo=getUserInfo($v['uid']);
            
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
	 		if($v['thumb']=="")
			{
				$v['thumb']=$v['avatar'];
			} 
			$nums=$redis->zSize('user_'.$v['stream']);
			$v['nums']=$nums;
            $recommend[$k]=$v;
		}

		$sort=array_column($recommend,"nums");
		$sort1=array_column($recommend,"uid");
		array_multisort($sort, SORT_DESC,$sort1,SORT_DESC, $recommend);

		
		$this->assign("recommend",$recommend);			 
			 
		/* 热门（在直播，推荐为热门） */
		$hot=M("users_live l")
					->field("uid,thumb,stream,title,city,islive,type")
					->where("islive='1' and ishot='1'")
					->order("isrecommend desc,starttime desc")
					->limit(10)
					->select();
        foreach($hot as $k=>$v){
            $userinfo=getUserInfo($v['uid']);

            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['signature']=$userinfo['signature'];
            $nums=$redis->zSize('user_'.$v['stream']);

            $v['nums']=(string)$nums;
            if($v['thumb']=="")
            {
                $v['thumb']=$v['avatar'];
            }
            $hot[$k]=$v;
		} 
		$this->assign("hot",$hot);

		/* 最新直播（在直播，按开播时间倒序） */ 
		$live=M("users_live")->field("uid,thumb,stream,title,city,islive,type")->where("islive='1'")->order("starttime desc")->limit(10)->select();
		foreach($live as $k=>$v){
            $userinfo=getUserInfo($v['uid']);
            
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
			$v['signature']=$userinfo['signature'];
            
			$nums=$redis->zSize('user_'.$v['stream']);
			$v['nums']=(string)$nums;

			if($v['thumb']==""){
				$v['thumb']=$v['avatar'];
			}
            $live[$k]=$v;
		} 

		$this->assign("live",$live);


		/* 主播排行榜 */
	  /*$anchorlist=M("users_liverecord")->field("uid,sum(nums) as light")->order("light desc")->group("uid")->limit(10)->select();
		foreach($anchorlist as $k=>$v){
			$anchorlist[$k]['userinfo']=getUserInfo($v['uid']);
			// 判断 当前用户是否关注
			if($uid>0){
				$isAttention=isAttention($uid,$v['uid']);
				$anchorlist[$k]['isAttention']=$isAttention;
			}else{
				$anchorlist[$k]['isAttention']=0;
			}
			
		}
		$this->assign("anchorlist",$anchorlist);*/

		

		$redis->close();
    	$this->display();
    }	


}


