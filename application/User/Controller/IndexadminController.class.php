<?php

/**
 * 会员
 */
namespace User\Controller;
use Common\Controller\AdminbaseController;
class IndexadminController extends AdminbaseController {
	
	protected $users_model;
	
	function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
	}

	
    function index(){
			
			$map=array();
			$map['user_type']=2;
			
			if($_REQUEST['iszombie']!=''){
				$map['iszombie']=$_REQUEST['iszombie'];
				$_GET['iszombie']=$_REQUEST['iszombie'];
			 }
			 
			 if($_REQUEST['isban']!=''){
				$map['user_status']=$_REQUEST['isban'];
				$_GET['isban']=$_REQUEST['isban'];
			 }
			 
			 if($_REQUEST['issuper']!=''){

				$map['issuper']=$_REQUEST['issuper'];
				$_GET['issuper']=$_REQUEST['issuper'];
			 }
             
             if($_REQUEST['source']!=''){

				$map['source']=$_REQUEST['source'];
				$_GET['source']=$_REQUEST['source'];
			 }
			 

			 if($_REQUEST['ishot']!=''){
				$map['ishot']=$_REQUEST['ishot'];
				$_GET['ishot']=$_REQUEST['ishot'];
			 }
			 
			 if($_REQUEST['iszombiep']!=''){
				$map['iszombiep']=$_REQUEST['iszombiep'];
				$_GET['iszombiep']=$_REQUEST['iszombiep'];
			 }
					 
			 if($_REQUEST['start_time']!=''){
				$map['create_time']=array("gt",$_REQUEST['start_time']);
				$_GET['start_time']=$_REQUEST['start_time'];
			 }
			 
			 if($_REQUEST['end_time']!=''){
				$map['create_time']=array("lt",$_REQUEST['end_time']);
				$_GET['end_time']=$_REQUEST['end_time'];
			 }
			 if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
				$map['create_time']=array("between",array($_REQUEST['start_time'],$_REQUEST['end_time']));
				$_GET['start_time']=$_REQUEST['start_time'];
				$_GET['end_time']=$_REQUEST['end_time'];
			 }

			 if($_REQUEST['keyword']!=''){
                 $keyword=trim($_REQUEST['keyword']);
				$where['id|user_login|user_nicename']	=array("like","%".$keyword."%");
				$where['_logic']	="or";
				$map['_complex']=$where;
				
				$_GET['keyword']=$keyword;
			 }
			
        $Agent_code=M('users_agent_code');

    	$users_model=$this->users_model;
    	$count=$users_model->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $users_model
            ->where($map)
            ->order("id DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach($lists as $k=>$v){
			$v['code']=$Agent_code->where("uid = {$v['id']}")->getField('code');
            $v['user_login']=m_s($v['user_login']);
            $v['mobile']=m_s($v['mobile']);
            $v['user_email']=m_s($v['user_email']);
            $lists[$k]=$v;
        }
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign('count', $count);
    	$this->assign("page", $page->show("Admin"));
    	
    	$this->display(":index");
    }
    function del(){
    	$id=intval($_GET['id']);
    	if ($id) {
            $userinfo=M("Users")->field("user_login")->where(array("id"=>$id,"user_type"=>2))->find();
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->delete();
    		if ($rst!==false) {
                    $action="删除会员：{$id} - {$userinfo['user_login']}";
                    setAdminLog($action);
					/* 删除认证 */
					M("users_auth")->where("uid='{$id}'")->delete();
                    /* 删除直播记录 */
					M("users_liverecord")->where("uid='{$id}'")->delete();
					/* 删除房间管理员 */
					M("users_livemanager")->where("uid='{$id}' or liveuid='{$id}'")->delete();
					/*  删除黑名单*/
					M("users_black")->where("uid='{$id}' or touid='{$id}'")->delete();
					/* 删除关注记录 */
					M("users_attention")->where("uid='{$id}' or touid='{$id}'")->delete();
					/* 删除僵尸 */
					M("users_zombie")->where("uid='{$id}'")->delete();
					/* 删除超管 */
					M("users_super")->where("uid='{$id}'")->delete();
					/* 删除会员 */
					M("users_vip")->where("uid='{$id}'")->delete();
					/* 删除分销关系 */
					M("users_agent")->where("uid='{$id}' or one_uid={$id}")->delete();
                    /* 删除分销邀请码 */
					M("users_agent_code")->where("uid='{$id}'")->delete();
					/* 删除坐骑 */
					M("users_car")->where("uid='{$id}'")->delete();
					/* 删除家族关系 */
					M("users_family")->where("uid='{$id}'")->delete();
                    
                    /* 删除推送PUSHID */
					M("users_pushid")->where("uid='{$id}'")->delete();
                    /* 删除钱包账号 */
					M("users_cash_account")->where("uid='{$id}'")->delete();
                    
                    /* 删除自己的标签 */
					M("users_label")->where("touid='{$id}'")->delete();
					
					/* 家族长处理 */
					$isexist=M("family")->field("id")->where("uid={$id}")->find();
					if($isexist){
						$data=array(
							'state'=>3,
							'signout'=>2,
							'signout_istip'=>2,
						);
						M("users_family")->where("familyid={$isexist['id']}")->save($data);				
						M("family_profit")->where("familyid={$isexist['id']}")->delete();		
						M("family_profit")->where("id={$isexist['id']}")->delete();		
					}
                /* 清除redis缓存 */
                delcache("userinfo_".$id,"token_".$id);
    			$this->success("会员删除成功！");
    		} else {
    			$this->error('会员删除失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }   
    function ban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_status','0');
    		if ($rst!==false) {
                $action="禁用会员：{$id}";
                setAdminLog($action);
				$nowtime=time();
				$redis = connectionRedis();
				$time=$nowtime + 60*60*1;
				$live=M("users_live")->field("uid")->where("islive='1'")->select();
				foreach($live as $k=>$v){
					$redis -> hSet($v['uid'] . 'shutup',$id,$time);
				}
				$redis -> close();	
    			$this->success("会员拉黑成功！");
    		} else {
    			$this->error('会员拉黑失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){ 
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_status','1');
    		if ($rst!==false) {

                /*$redis = connectionRedis();
                $live=M("users_live")->field("uid")->where("islive='1'")->select();
                foreach($live as $k=>$v){
                    $redis -> hdel($v['uid'].'shutup',$id);
                }
                $redis -> close();*/

                $action="启用会员：{$id}";
                setAdminLog($action);
    			$this->success("会员启用成功！");
    		} else {
    			$this->error('会员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }   

	function cancelsuper(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('issuper','0');
			$rst = M("users_super")->where(["uid"=>$id])->delete();
    		if ($rst!==false) {
                $action="取消超管会员：{$id}";
                setAdminLog($action);
				$redis = connectionRedis();
				$redis  -> hDel('super',$id);
				$redis -> close();
    			$this->success("会员取超管成功！");
    		} else {
    			$this->error('会员取消超管失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function super(){ 
    	$id=intval($_GET['id']);
    	if ($id) {
			$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('issuper','1');
    		$rst = M("users_super")->add(array('uid'=>$id,'addtime'=>time()));
    		if ($rst!==false) {
                $action="设置超管会员：{$id}";
                setAdminLog($action);
				$redis = connectionRedis();
				$redis  -> hset('super',$id,'1');
				$redis -> close();
    			$this->success("会员设置超管成功！");
    		} else {
    			$this->error('会员设置超管失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

	function cancelhot(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('ishot','0');
    		if ($rst!==false) {
                M("users_live")->where(array("uid"=>$id))->setField('ishot','0');
                $action="取消热门会员：{$id}";
                setAdminLog($action);
    			$this->success("会员取消热门成功！");
    		} else {
    			$this->error('会员取消热门失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function hot(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('ishot','1');
    		if ($rst!==false) {
                M("users_live")->where(array("uid"=>$id))->setField('ishot','1');
                $action="设置热门会员：{$id}";
                setAdminLog($action);
    			$this->success("会员设置热门成功！");
    		} else {
    			$this->error('会员设置热门失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }	
	
	function cancelrecommend(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('isrecommend','0');
    		if ($rst!==false) {
                M("users_live")->where(array("uid"=>$id))->setField('isrecommend','0');
                $action="取消推荐会员：{$id}";
                setAdminLog($action);
    			$this->success("会员取消推荐成功！");
    		} else {
    			$this->error('会员取消推荐失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function recommend(){ 
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('isrecommend','1');
    		if ($rst!==false) {
                M("users_live")->where(array("uid"=>$id))->setField('isrecommend','1');
                $action="设置推荐会员：{$id}";
                setAdminLog($action);
    			$this->success("会员推荐成功！");
    		} else {
    			$this->error('会员推荐失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
		
    function cancelzombie(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('iszombie','0');
    		if ($rst!==false) {
                $action="关闭会员僵尸粉：{$id}";
                setAdminLog($action);
    			$this->success("关闭成功！");
    		} else {
    			$this->error('关闭失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function zombie(){ 
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('iszombie','1');
    		if ($rst!==false) {
                $action="开启会员僵尸粉：{$id}";
                setAdminLog($action);
    			$this->success("开启成功！");
    		} else {
    			$this->error('开启失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }	
    function zombieall(){ 
    	$iszombie=intval($_GET['iszombie']);

    		$rst = M("Users")->where("user_type='2'")->setField('iszombie',$iszombie);
    		if ($rst!==false) {
                if($iszombie==1){
                    $action="开启全部会员僵尸粉";
                }else{
                    $action="关闭全部会员僵尸粉";
                }
                
                setAdminLog($action);
    			$this->success("操作成功！");
    		} else {
    			$this->error('操作失败！');
    		}

    }				
		
    function cancelzombiep(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('iszombiep','0');
    		if ($rst!==false) {
                $action="关闭僵尸粉会员：{$id}";
                setAdminLog($action);
				M("users_zombie")->where("uid='{$id}'")->delete();
    			$this->success("关闭成功！");
    		} else {
    			$this->error('关闭失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function zombiep(){ 
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('iszombiep','1');
    		if ($rst!==false) {
                $action="开启僵尸粉会员：{$id}";
                setAdminLog($action);
				$users_zombie=M("users_zombie");
				$isexist=$users_zombie->where("uid={$id}")->find();
				if(!$isexist){
					$users_zombie->add(array("uid"=>$id));	
				}
    			$this->success("开启成功！");
    		} else {
    			$this->error('开启失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }			
		
    //批量设置僵尸粉
    public function zombiepbatch() {
		$iszombiep=intval($_GET['iszombiep']);
		$ids = $_POST['ids'];
		$tids=join(",",$_POST['ids']);
		$users_zombie=M("users_zombie");
        $where['id']=['in',$_POST['ids']];
        $where['user_type']=2;
		$rst = M("Users")->where($where)->setField('iszombiep',$iszombiep);
		if ($rst!==false) {
			if($iszombiep==1){
				foreach($ids as $k=>$v){
					$isexist=$users_zombie->where("uid={$v}")->find();
					if(!$isexist){
						$users_zombie->add(array("uid"=>$v));	
					}
					
				}
				$action="开启会员僵尸粉：{$tids}";
			}else{
                $where2['uid']=['in',$_POST['ids']];
				$users_zombie->where($where2)->delete();
                $action="关闭会员僵尸粉：{$tids}";
			}
            setAdminLog($action);
			$this->success("设置成功！");
		} else {
			$this->error('设置失败！');
		}
    }				
		
    function cancelrecord(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('isrecord','0');
    		if ($rst!==false) {
                $action="关闭会员回放：{$id}";
                setAdminLog($action);
    			$this->success("关闭成功！");
    		} else {
    			$this->error('关闭失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function record(){ 
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('isrecord','1');
    		if ($rst!==false) {
                $action="开启会员回放：{$id}";
                setAdminLog($action);
    			$this->success("开启成功！");
    		} else {
    			$this->error('开启失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }	
    function recordall(){ 
    	$isrecord=intval($_GET['isrecord']);

    		$rst = M("Users")->where("user_type='2'")->setField('isrecord',$isrecord);
    		if ($rst!==false) {
                if($isrecord==1){
                    $action="开启全部会员回放：";
                }else{
                    $action="关闭全部会员回放：";
                }
                
                setAdminLog($action);
    			$this->success("操作成功！");
    		} else {
    			$this->error('操作失败！');
    		}

    }				
	function add(){
		$this->display(":add");				
	}
	
	function add_post(){
		if(IS_POST){			
			$user=$this->users_model;
			$user_login=I('user_login');
            if($user_login==''){
                $this->error('手机号不能为空');
            }
            $isexist=M("users")->field("id")->where(["user_login"=>$user_login])->find();
            if($isexist){
                $this->error('手机号已存在');
            }
			if( $user->create()){
				$user->user_type=2;
				$user->user_pass=sp_password($_POST['user_pass']);
				$avatar=$_POST['avatar'];
				
				if($avatar==''){
					$user->avatar= '/default.jpg'; 
					$user->avatar_thumb= '/default_thumb.jpg'; 
				}else if(strpos($avatar,'http')===0){
					/* 绝对路径 */
					$user->avatar=  $avatar; 
					$user->avatar_thumb=  $avatar;
				}else if(strpos($avatar,'/')===0){
					/* 本地图片 */
					$user->avatar=  $avatar;
					$user->avatar_thumb=  $avatar; 
				}else{
					/* 七牛 */
					//$user->avatar=  $avatar.'?imageView2/2/w/600/h/600'; //600 X 600
					//$user->avatar_thumb=  $avatar.'?imageView2/2/w/200/h/200'; // 200 X 200
				}

				$user->create_time=date('Y-m-d H:i:s',time());
				$result=$user->add();
				if($result!==false){
                    $code=createCode();
                    $code_info=array('uid'=>$result,'code'=>$code);
                    $Agent_code=M('users_agent_code');
                    $isexist=$Agent_code
                                ->field("uid")
                                ->where("uid = {$result}")
                                ->find();
                    if($isexist){
                        $Agent_code->where("uid = {$result}")->save($code_info);	
                    }else{
                        $Agent_code->add($code_info);
                    }
        
                    $action="添加会员：{$result}";
                    setAdminLog($action);
					$this->success('添加成功');
				}else{
					$this->error('添加失败');
				}					 
				 
			}else{
				$this->error($this->users_model->getError());
			}
		}			
	}		
	function edit(){
		$id=intval($_GET['id']);
		if($id){
			$userinfo=M("users")->find($id);

            $userinfo['user_login']=m_s($userinfo['user_login']);
            $userinfo['mobile']=m_s($userinfo['mobile']);
            $userinfo['user_email']=m_s($userinfo['user_email']);

			$this->assign('userinfo', $userinfo);						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display(":edit");				
	}
	
	function edit_post(){
		if(IS_POST){			
			$user=M("users");
			$user->create();
			$avatar=$_POST['avatar'];
			$id=$_POST['id'];
			if($avatar==''){
				$user->avatar= '/default.jpg'; 
				$user->avatar_thumb= '/default_thumb.jpg'; 
			}else if(strpos($avatar,'http')===0){
				/* 绝对路径 */
				$user->avatar=  $avatar; 
				$user->avatar_thumb=  $avatar;
			}else if(strpos($avatar,'/')===0){
				/* 本地图片 */
				$user->avatar=  $avatar; 
				$user->avatar_thumb=  $avatar; 
			}else{
				/* 七牛 */
				//$user->avatar=  $avatar.'?imageView2/2/w/600/h/600'; //600 X 600
				//$user->avatar_thumb=  $avatar.'?imageView2/2/w/200/h/200'; // 200 X 200
			}
			 $result=$user->save(); 
			 if($result!==false){
                $this->delCache($id);
                $action="修改会员信息：{$id}";
                setAdminLog($action);
                $key='';
                $this->success('修改成功');
			 }else{
                $this->error('修改失败');
			 }
		}			
	}
	/* 生成邀请码 */
	function createCode(){
		$code=createCode();
		$rs=array('info'=>$code);
		echo json_encode($rs);
		exit;
	}
    public function delCache($uid){
        $key='userinfo_'.$uid;
        delcache($key);
    }		
}
