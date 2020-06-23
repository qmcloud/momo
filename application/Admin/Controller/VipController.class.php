<?php

/**
 * VIP管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class VipController extends AdminbaseController {

	public $long=array(
		'1'=>'1个月',
		'3'=>'3个月',
		'6'=>'6个月',
		'12'=>'12个月',
	);
    function index(){	
    	$Vip=M("vip");
    	$count=$Vip->count();
    	$page = $this->page($count, 20);
    	$lists = $Vip
    	->order("orderno asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign('long', $this->long);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
	function del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("vip")->delete($id);				
				if($result){
                    $action="删除VIP：{$id}";
                    setAdminLog($action);
						$this->success('删除成功');
				 }else{
						$this->error('删除失败');
				 }			
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}		
    //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("vip")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新VIP排序";
            setAdminLog($action);
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    

	function add(){
			$this->assign('long', $this->long);
			$this->display();				
	}	
	function add_post(){
		if(IS_POST){			
			$Vip=M("vip");
			$length=I("length");
			
            $where['length']=$length;
			$isexist=$Vip->where($where)->find();
			
			if($isexist){
				$this->error('已存在相同类型 时长的设置');
			}
			
			 $Vip->create();
			 $Vip->addtime=time();
			 $result=$Vip->add(); 
			 if($result!==false){
                    $action="添加VIP：{$result}";
                    setAdminLog($action);
				  $this->success('添加成功');
			 }else{
				  $this->error('添加失败');
			 }
		}			
	}		
	function edit(){
		$id=intval($_GET['id']);
		if($id){
			$vip=M("vip")->find($id);
			$this->assign('vip', $vip);		
			$this->assign('long', $this->long);
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function edit_post(){
		if(IS_POST){	
			$Vip=M("vip");
			//$length=I("length");
			$id=I("id");
			
			/* 
            $where['length']=$length;
            $where['id']=array('neq',$id);
            $isexist=$Vip->where($where)->find();
			if($isexist){
				$this->error('已存在相同类型 时长的设置');
			} */
			
			
			$Vip->create();
			$result=$Vip->save(); 
			if($result!==false){
                $action="修改VIP：{$id}";
                setAdminLog($action);
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}			
	}
		
    function user_index(){
	
    	$Vip_u=M("users_vip");
    	$count=$Vip_u->count();
    	$page = $this->page($count, 20);
    	$lists = $Vip_u
    	->order("endtime desc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
		foreach($lists as $k=>$v){
			$lists[$k]['userinfo']=getUserInfo($v['uid']);
		}
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));

    	$this->display();
    }		
		
	function user_del(){
		$id=intval($_GET['id']);
		if($id){
            $data	=M("users_vip")->where("id={$id}")->find();
			$result=M("users_vip")->delete($id);				
			if($result){
                $action="删除用户VIP：{$data['uid']}";
                setAdminLog($action);
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}		
			
    function user_add(){	
    	$this->display();
    }		
	function do_user_add(){

		if(IS_POST){	
			$uid=(int)$_POST['uid'];
            if($uid==''){
				$this->error('用户ID不能为空');
			}
			$isexist=M("users")->field("id")->where("id={$uid}")->find();
			if(!$isexist){
				$this->error('该用户不存在');
			}
			
			$Vip_u=M("users_vip");
            $where['uid']=$uid;
			$isexist2=$Vip_u->field("id")->where($where)->find();
			if($isexist2){
				$this->error('该用户已购买过会员');
			}
			
			$Vip_u->create();
			$Vip_u->addtime=time();
			$Vip_u->endtime=strtotime($_POST['endtime']);
			$result=$Vip_u->add(); 
			if($result!==false){
                $action="添加用户VIP：{$uid}";
                setAdminLog($action);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}				
    }		
    function user_edit(){

		$id=intval($_GET['id']);
		if($id){
			$data	=M("users_vip")->where("id={$id}")->find();
			$data['userinfo']=getUserInfo($data['uid']);
			$this->assign('data', $data);	
		}else{				
			$this->error('数据传入失败！');
		}								      	
    	$this->display();
    }			
	function do_user_edit(){
		if(IS_POST){			
			$Vip_u=M("users_vip");
            $id=(int)$_POST['id'];
            $where['id']=$id;
            $data	=$Vip_u->where($where)->find();
            
			$Vip_u->create();
			$Vip_u->endtime=strtotime($_POST['endtime']);
			$result=$Vip_u->save(); 
			if($result!==false){
                $action="修改用户VIP：{$data['uid']}";
                setAdminLog($action);
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}	
    }
}
