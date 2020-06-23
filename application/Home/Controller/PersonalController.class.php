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

class PersonalController extends HomebaseController {
  /**个人中心-首页方法**/
	public function index() {
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$getgif=getgif($uid);
		$this->assign("getgif",$getgif[0]);

		$this->display();
    }
	/**个人中心-头部修改昵称**/
	public function edit_name()
	{
		$User=M("users");
		$uid=(int)session("uid");
		$name=urldecode(I("name"));
        
        if($uid<1){
            echo '{"state":"1"}';
			exit;            
        }
        $where['id']=$uid;
		$userinfo= $User->where($where)->setField("user_nicename", $name);
		$_SESSION['user']['user_nicename']=  $name;
		if($userinfo)
		{
			echo '{"state":"0"}';
		}
		else
		{
			echo '{"state":"1"}';
		}
		exit;
	}
	 /**
	个人中心-基本资料展示
	**/
	public function modify() 
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$this->assign("personal",'Set');
		$this->display();
    }
	 /**
	个人中心-基本资料修改
	**/
	public function edit_modify()
   {
	  $User=M("users");
	  $uid=(int)session("uid");
	  $token=session("token");
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			echo '{"state":"0","msg":"登录失效,请重新登录"}';
			exit;
		}
		$birthday=I('birthday');
		$user_nicename=urldecode(I('nickName'));
		$sex=I('sex');
		$signature=I('signature');
        
		 $data=array(
			"id"=>$uid,
			"birthday"=>$birthday,
			"user_nicename"=>$user_nicename,
			"sex"=> $sex,
			"signature"=>$signature
		 );
		$result=$User->save($data);
		if($result!==false)
		{
			$_SESSION['user']['user_nicename']= $user_nicename;
			$_SESSION['user']['sex']= $sex;
			$_SESSION['user']['signature']= $signature;
			echo '{"state":"0","msg":"修改成功"}';
			exit;
		}
		else
		{
			 echo '{"state":"1","msg":"修改失败"}';
		}
		exit;
   }
    /**
	个人中心-头像展示
	**/
	public function photo()
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$this->assign("personal",'Set');
		$this->display();
	}
	/**个人中心-修改头像**/
	public function edit_photo()
	{
		$user=M("users");
		$uid=session("uid");
		$token=session("token");
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			$callback = array(
				'error' => 0,
				'type'  => "登录失效,请重新登录"
				);
			echo json_encode($callback);
			exit;
		}
		$url=urldecode(I('avatar'));
		if (!empty($url)) {
			$avatar=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
			$avatar_thumb=  $url.'?imageView2/2/w/200/h/200'; // 200 X 200
			$data=array(
					"id"=>$uid,
					"avatar"=>$avatar,
					"avatar_thumb"=>$avatar_thumb,
				);
			$result=$user->save($data); 
			$_SESSION['user']['avatar']=urldecode($data['avatar']);
			$_SESSION['user']['avatar_thumb']=urldecode($data['avatar_thumb']);
			if($result!==false)
			{
				$callback = array(
				'error' => 1,
				'type'  => "头像修改成功"
				);
			}
			else{
				$callback = array(
				'error' => 0,
				'type'  => "头像修改失败"
				);
			}		
		}
		else
		{
			$callback = array(
				'error' => 0,
				'type'  => "图片处理失败"
			);
		}
		echo json_encode($callback);
		exit;
	}
	/**个人中心-我的认证**/
	public function card()
	{
		LogIn();
		$uid=(int)session("uid");
		$this->assign("uid",$uid);
        $where['uid']=$uid;
		$auth=M("users_auth")->where($where)->find();
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$this->assign("auth",$auth);
		$this->assign("personal",'card');
		$this->display();
	}
	/**
	个人中心-我的认证-身份证上传
	$info判断上传状态
	**/
	function upload(){
		$saveName=I('saveName')."_".time(); 
    	$config=array(
			    'replace' => true,
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => '/rz/',
    			'maxSize' => 0,//500K
    			'saveName'   =>    $saveName,
    			//'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);
    	$upload = new \Think\Upload($config);//
    	$info=$upload->upload();
     	//开始上传
    	if ($info) {
				//上传成功
				//写入附件数据库信息
    		$first=array_shift($info);

			if(!empty($first['url'])){
				$url=$first['url'];
			}else{
				$url=C("TMPL_PARSE_STRING.__UPLOAD__").'rz/'.$first['savename'];
			}
				
    		echo json_encode(array("ret"=>200,'data'=>array("url"=>$url),'msg'=>$saveName));
    		//$this->ajaxReturn(sp_ajax_return(array("file"=>$file),"上传成功！",1),"AJAX_UPLOAD");
    	} else {
    		//上传失败，返回错误
    		//$this->ajaxReturn(sp_ajax_return(array(),$upload->getError(),0),"AJAX_UPLOAD");
				  echo json_encode(array("ret"=>0,'file'=>'','msg'=>$upload->getError()));
    	}	
		exit;

	}
	/**
	个人中心-我的认证-认证信息写入数据库
	**/
	function authsave()
	{ 
		$data['uid']=(int)session("uid");
		$data['real_name']=I("real_name");
		$data['mobile']=I("mobile");
		$data['cer_no']=I("cer_no");
		$data['front_view']=I("front_view");
		$data['back_view']=I("back_view");
		$data['handset_view']=I("handset_view");
		$data['status']=0;
		$data['addtime']=time();
        
        if($data['uid']<1){
            echo json_encode(array("ret"=>0,'data'=>array(),'msg'=>'参数错误'));
			exit;            
        }
		$authid=M("users_auth")->where("uid='{$data['uid']}'")->getField('uid');
		if($authid)
		{
			$result=M("users_auth")->where("uid='{$authid}'")->save($data);
		}
		else
		{
			$result=M("users_auth")->add($data);
		}
	  if($result!==false)
		{		
			echo json_encode(array("ret"=>200,'data'=>array(),'msg'=>''));
		}
		else
		{		
			echo json_encode(array("ret"=>0,'data'=>array(),'msg'=>'提交失败，请重新提交'));
		}	
		exit;		
	}	
	/**
	个人中心-我关注的
	**/
  public function follow()
	{
		LogIn();
		$uid=(int)session("uid");
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$live=M("users_attention");
        $where['uid']=$uid;
		$attention=$live->where($where)->select();
		foreach($attention as $k=>$v)
		{
			$users=getUserInfo($v['touid']);
			$attention[$k]['users']=$users;
            $attention[$k]['follow']=getFollownums($v['touid']);
            $attention[$k]['fans']=getFansnums($v['touid']);
		}
		$this->assign("attention",$attention);
		$this->assign("personal",'follow');
		$this->display();
	}
	/**
	个人中心-我关注的-取消关注
	**/
	public function follow_dal()
	{
		$live=M("users_attention");
		$touid=(int)I('followID');
		$uid=(int)session("uid");
        
        if($uid<1 || $touid<1){
			echo '{"state":"1","msg":"参数错误"}';
			exit;            
        }
        $where['touid']=$touid;
        $where['uid']=$uid;
        
		$del_follow=$live->where($where)->delete();
		if($del_follow!==false)
		{
			echo '{"state":"0","msg":"取消关注"}';
		}
		else
		{
			echo '{"state":"1","msg":"取消失败"}';
		}
	}
	public function follow_add()
	{
		$touid=(int)I('touid');
		$uid=(int)session("uid");
        if($uid<1 || $touid<1){
			echo '{"state":"1","msg":"参数错误"}';
			exit;            
        }
		$data=array(
			"uid"=>$uid,
			"touid"=>$touid
		);
		$result=M("users_attention")->add($data);
		if($result!==false)
		{
			M('users_black')->where($data)->delete();
			echo '{"state":"0","msg":"关注成功"}';
		}
		else
		{
			echo '{"state":"1","msg":"关注失败"}';
		}
	}
	/**
	个人中心-我的粉丝
	**/
	public function fans()
	{
		LogIn();
		$uid=(int)session("uid");
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$live=M("users_attention");
        $where['touid']=$uid;
        
		$attention=$live->where($where)->select();
		foreach($attention as $k=>$v)
		{
			$users=getUserInfo($v['uid']);
			$attention[$k]['users']=$users;
      		$attention[$k]['follow']=getFollownums($v['uid']);
      		$attention[$k]['fans']=getFansnums($v['uid']);
			$isAttention=isAttention($uid,$v['uid']);
			$attention[$k]['attention']=$isAttention;
			$attention[$k]['isblack']=isBlack($uid,$v['uid']);
			
		}
		$this->assign("attention",$attention);
		$this->assign("personal",'follow');
		$this->display();
	}

	/*黑名单*/
	public function namelist()
	{
		LogIn();
		$uid=(int)session("uid");
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$live=M("users_black");
        
        $where['uid']=$uid;
        
		$attention=$live->where($where)->select();
		foreach($attention as $k=>$v)
		{
			$users=getUserInfo($v['touid']);
			$attention[$k]['users']=$users;
            $attention[$k]['follow']=getFollownums($v['touid']);
            $attention[$k]['fans']=getFansnums($v['touid']);
			$isAttention=isAttention($uid,$v['touid']);
			$attention[$k]['attention']=$isAttention;
		}
		$this->assign("attention",$attention);
		$this->assign("personal",'follow');
		$this->display();
	}
	/*删除黑名单*/
	public function list_del()
	{
		$uid=(int)session("uid");
		$touid=(int)I('touid');
        
        if($uid<1 || $touid<1){
            echo '{"state":"1000","msg":"参数错误"}';
			exit;            
        }
        
		$isBlack=isBlack($uid,$touid);
		if($isBlack==0)
		{
			echo '{"state":"1000","msg":"该用户不在你的黑名单内"}';
			exit;
		}
		else
		{
            $where['touid']=$touid;
            $where['uid']=$uid;
            
			$attention=M('users_black')->where($where)->delete();
			if($attention)
			{
				echo '{"state":"0","msg":"移除成功"}';
				exit;
			}
			else
			{
				echo '{"state":"1001","msg":"移除失败"}';
				exit;
			}
		}
	}
	/*拉黑操作 如果我已经关注这个主播 同时会删除关注状态但是不会清除粉丝*/

	public function blacklist(){
		$uid=(int)session("uid");
		$touid=(int)I("touid");
        
        if($uid<1 || $touid<1){
            echo '{"state":"1000","msg":"参数错误"}';
			exit;            
        }
        
        
		$isBlack=isBlack($uid,$touid);
		if($isBlack==1)
		{
			echo '{"state":"1000","msg":"你已经将该用户拉黑"}';
			exit;
		}
		else
		{
			$isAttention=isAttention($uid,$touid);
			if($isAttention)
			{
                $where['touid']=$touid;
                $where['uid']=$uid;
                
				M('users_attention')->where($where)->delete();
			}
			$data=array(
				"uid"=>$uid,
				"touid"=>$touid
			);


			$result=M('users_black')->add($data);

			if($result)
			{
				echo '{"state":"0","msg":"拉黑成功"}';
				exit;
			}
			else
			{
				echo '{"state":"1001","msg":"拉黑失败"}';
				exit;
			}
		}	
	}
	/**
	个人中心-管理员管理中心
	**/
	public function admin()
	{
		LogIn();
		$uid=(int)session("uid");
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$live=M("users_livemanager");
        
        $where['liveuid']=$uid;
        
		$admin=$live->where($where)->select();
		foreach($admin as $k=>$v)
		{
			$users=getUserInfo($v['uid']);
			$admin[$k]['users']=$users;
            $admin[$k]['follow']=getFollownums($v['uid']);
            $admin[$k]['fans']=getFansnums($v['uid']);
			$isAttention=isAttention($uid,$v['uid']);
			$admin[$k]['attention']=$isAttention;
		}
		$this->assign("admin",$admin);
		$this->assign("personal",'follow');
		$this->display();
	}
	/**
	个人中心-管理员管理中心-取消管理员
	users_livemanager管理员记录表
	**/
	function admin_del()
	{ 
		$uid=(int)session("uid");
		$touid=(int)I('touid');
        if($uid<1 || $touid<1){
            echo '{"state":"1000","msg":"参数错误"}';
			exit;            
        }
        
        if($touid) 
		{
            $where['uid']=$touid;
            $where['liveuid']=$uid;
            
            $rst = M("users_livemanager")->where($where)->delete();
            if ($rst) 
                {
                echo '{"state":"0","msg":"管理取消成功"}';
                exit;
            } 
            else{
                echo '{"state":"1000","msg":"管理取消失败"}';
                exit;
            }
        } 
		else 
		{
    		echo '{"state":"1001","msg":"数据传入失败"}';
				exit;
    }
  }
	/**
	个人中心-提现中心
	
	**/
	public function exchange()
	{
		LogIn();
		$uid=(int)session("uid");
		$token=session("token");
		$info=getUserPrivateInfo($uid);	

		$config=getConfigPri();
		//提现比例
		$cash_rate=$config['cash_rate'];
		$cash_start=$config['cash_start'];
		$cash_end=$config['cash_end'];
		$cash_max_times=$config['cash_max_times'];
		//剩余票数
		$votes=$info['votes'];
		$votestotal=$info['votestotal'];
			
		//总可提现数
		$total=floor($votes/$cash_rate);
        
        if($cash_max_times){
            $tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放，每月只可提现'.$cash_max_times.'次';
        }else{
            $tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放';
        }
		
        
		$rs=array(
			"votes"=>$votes,
			"votestotal"=>$votestotal,
			"todaycash"=>$votes,
			"total"=>$total,
			"cash_rate"=>$cash_rate,
			"tips"=>$tips,
		);
		$zlist=M('users_cash_account')->where("uid={$uid}")
                ->order("addtime desc")
                ->select();
		$type=array(
			'1'=>"支付宝",
			'2'=>"微信",
			'3'=>"银行卡",
		);
		foreach($zlist as $k=>$v){
			$zlist[$k]['type_account']=$type[$v['type']]."-".$v['account'];
		}
		$this->assign("token",$token);
		
		$this->assign("uid",$uid);
	 	$this->assign("zlist",$zlist);
	 	$this->assign("info",$info);
	 	$this->assign("rs",$rs);
		$this->assign('exchange_rate',$exchange_rate);
		$this->assign("personal",'card');
		$this->display();
	}
	/**
	个人中心-提现中心开始提现
	**/
	public function edit_exchange(){
		
		
		$uid=(int)session("uid");
		$token=session("token");
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			echo '{"code":"1003","msg":"您的登陆状态失效，请重新登陆！"}';
			exit;
		}
		
        $where['uid']=$uid;
        
		$isrz=M("users_auth")->field("status")->where($where)->find();
		if(!$isrz || $isrz['status']!=1){
			echo '{"code":"1003","msg":"请先进行身份认证"}';
			exit;
		}
		
		$info=getUserPrivateInfo($uid);	
		
		$nowtime=time();
        $accountid=I('accountid');
        $cashvote=I('cashvote');
        $config=getConfigPri();
        $cash_start=$config['cash_start'];
        $cash_end=$config['cash_end'];
        $cash_max_times=$config['cash_max_times'];
        
        $day=(int)date("d",$nowtime);
        if($day < $cash_start || $day > $cash_end){
            echo '{"code":"1005","msg":"不在提现期限内，不能提现"}';
			exit;
        }
        
        //本月第一天
        $month=date('Y-m-d',strtotime(date("Ym",$nowtime).'01'));
        $month_start=strtotime(date("Ym",$nowtime).'01');

        //本月最后一天
        $month_end=strtotime("{$month} +1 month");      

        if($cash_max_times){
            $isexist=M('users_cashrecord')
                    ->where("uid={$uid} and addtime > {$month_start} and addtime < {$month_end}")
                    ->count();
            if($isexist > $cash_max_times){
                echo '{"code":"1006","msg":"每月只可提现'.$cash_max_times.'次,已达上限"}';
                exit;
            }   
        }

		
        /* 钱包信息 */
		$accountinfo=M('users_cash_account')
				->where("id={$accountid}")
				->find();
        if(!$accountinfo){
            echo '{"code":"1006","msg":"该钱包不存在"}';
			exit;
        }
		
		$votes=$info['votes'];
        
        if($cashvote > $votes){
            echo '{"code":"1001","msg":"余额不足"}';
			exit;
        }
        

		//提现比例
		$cash_rate=$config['cash_rate'];
		/* 最低额度 */
		$cash_min=$config['cash_min'];
		
		//提现钱数
        $money=floor($cashvote/$cash_rate);
		
		if($money < $cash_min){
            echo '{"code":"1001","msg":"提现最低额度为'.$cash_min.'元"}';
			exit;
		}
		
		$cashvotes=$money*$cash_rate;
		$data=array(
			"uid"=>$uid,
			"money"=>$money,
			"votes"=>$cashvotes,
			"orderno"=>$uid.'_'.$nowtime.rand(100,999),
			"status"=>0,
			"addtime"=>$nowtime,
			"uptime"=>$nowtime,
			"type"=>$accountinfo['type'],
			"account_bank"=>$accountinfo['account_bank'],
			"account"=>$accountinfo['account'],
			"name"=>$accountinfo['name'],
		);
		
		$rs=M("users_cashrecord")->add($data);
		if($rs){
			M("users")->where("id={$uid}")->setDec('votes',$cashvotes); 
			echo '{"code":"0","msg":"提现成功"}';
			exit;
		}else{
			echo '{"code":"1002","msg":"提现失败，请重试"}';
			exit;
		}	
	}
	
	/* 提现记录*/
	var $status=array(
        '0'=>'审核中',
        '1'=>'成功',
        '2'=>'失败',
    );
	public function cash_list(){
		$uid=(int)session("uid");
		LogIn();
		$info=getUserPrivateInfo($uid);	
		$pagesize = 20; 
        $where['uid']=$uid;
		$count= M('users_cashrecord')
				->where($where)
				->count();
		$Page= new \Page2($count,$pagesize);
		$show= $Page->show();
		$list=M("users_cashrecord")
			->where($where)
			->order("addtime desc")
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		foreach($list as $k=>$v){

			$list[$k]['addtime']=date('Y.m.d',$v['addtime']);
			$list[$k]['status_name']=$this->status[$v['status']];
		}
		
		$this->assign("info",$info);
		$this->assign('page',$show);
		$this->assign("list",$list);
		$this->display();
	}
	
	
		/**
	个人中心-账号管理
	
	**/
	public function account_list()
	{
		LogIn();
		$uid=(int)session("uid");
		$token=session("token");
		$pagesize = 20; 
		$info=getUserPrivateInfo($uid);	
        $where['uid']=$uid;
		$count= M('users_cash_account')
				->where($where)
				->count();
		$Page= new \Page2($count,$pagesize);
		$show= $Page->show();
		$list=M('users_cash_account')
				->where($where)
                ->order("addtime desc")
               ->limit($Page->firstRow.','.$Page->listRows)
			   ->select();
		
		foreach($list as $k=>$v){
			if($v['type']==1){
				$list[$k]['type_account']="支付宝";
				$list[$k]['account_bank']="-----";
			}else if($v['type']==2){
				$list[$k]['type_account']="微信";
				$list[$k]['account_bank']="-----";
				$list[$k]['name']="-----";
			}else if($v['type']==3){
				$list[$k]['type_account']="银行卡";
			}
		}
		$this->assign("token",$token);
		
		$this->assign("uid",$uid);
		$this->assign('page',$show);
		$this->assign("info",$info);
	 	$this->assign("list",$list);
		$this->display();
	}
	/*修改密码*/
	public function updatepass(){
		LogIn();
        $uid=(int)session("uid");
		$info=getUserPrivateInfo($uid);	
		$this->assign("info",$info);
		$this->assign("personal",'Set');
		$this->display();
	}
	/* 执行密码修改 */
	public function savepass() {
		$uid=(int)session("uid");
		//旧密码
		$oldpass = I('oldpass');
		//新密码
		$newpass = I('newpass');
		//确认密码
		$repass = I('repass');
		$rs=array();
		if($newpass !== $repass)
		{
			$rs['code'] = 800;
			$rs['msg'] = '两次密码不一致';
			echo json_encode($rs);
			exit;
		}
		$oldpass = setPass($oldpass);
		$pwd = setPass($newpass);
		$check =passcheck($newpass); 
		if($check==0)
		{
			$rs['code'] = 1001;
			$rs['msg'] = '密码6-12位数字与字母';
			echo json_encode($rs);
			exit;			
		}	
		if($check==2)
		{
			$rs['code'] = 1002;
			$rs['msg'] = '密码6-12位数字与字母';
			echo json_encode($rs);
			exit;			
		}			
		/* 密码判定 */
        $where['id']=$uid;
        $where['user_pass']=$oldpass;
        $where['user_type']=2;
		$rt=M("users")->where($where)->find();
		if(empty($rt)){
			$rs['code'] = 103;
			$rs['msg'] = '旧密码错误';
			echo json_encode($rs);
			exit;	
		}
		$data=array();
		$User = M("users"); 
		//要修改的数据对象属性赋值
		$data['user_pass'] = $pwd;
		$map['id'] =$uid;
		//保存昵称到数据库
		$result=$User->where($map)->save($data);
		if($result!==false){
			$rs['code'] = 0;
			$rs['msg'] = '修改成功';
			echo json_encode($rs);
			exit;
		}else{
			$rs['code'] = 0;
			$rs['msg'] = '修改失败';
			echo json_encode($rs);
			exit;
		}
		
		
  }
	/**
	个人中心-直播记录
	**/
	public function live()
	{
		$uid=(int)session("uid");
		LogIn();
	 	$where=array();
		$where['uid']=$uid;
        $start_time=I('start_time');
        $end_time=I('end_time');
        
		if($start_time!='')
		{
			$where['starttime']=array("gt",strtotime($start_time));
			$_GET['start_time']=$start_time;
		}
		if($end_time!='')
		{ 
			$where['starttime']=array("lt",strtotime($end_time));
			$_GET['end_time']=$end_time;
		}
		if($start_time!=''&& $end_time!='' )
		{	 
			$where['starttime']=array("between",array(strtotime($start_time),strtotime($end_time)));
			$_GET['start_time']=$start_time;
			$_GET['end_time']=$end_time;
		} 
		$this->assign('formget', $_GET);
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$pagesize = 20; 
		$User = M('users_liverecord');
		$Live = M('users');
        
        $where2['id']=$uid;
        
		$coin=$Live->where($where2)->getField("coin");
		$this->assign('coin',$coin);
		$count= $User->where($where)->count();
		$Page= new \Page2($count,$pagesize);
		$show= $Page->show();
		$lists = $User->field($this->field)->where($where)->order("showid desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('lists',$lists);
		$this->assign('page',$show);
		$this->assign('uid',$uid);
		$this->assign("personal",'follow');
		$this->display();
	}	
}


