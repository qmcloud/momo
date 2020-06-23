<?php

/**
 * 登录奖励
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LoginbonusController extends AdminbaseController {
    function index(){

    	$Loginbonus=M("loginbonus");
    	$count=$Loginbonus->count();
    	$page = $this->page($count, 20);
    	$lists = $Loginbonus
    	->order("day asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("loginbonus")->delete($id);				
							if($result){
                                $action="删除登录奖励：{$id}";
                    setAdminLog($action);
                    $this->resetcache();
									$this->success('删除成功');
							 }else{
									$this->error('删除失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}		
	

		function add(){
				$this->display();				
		}	
		function add_post(){
				if(IS_POST){			
					 $bonus=M("loginbonus");
					 $bonus->create();
					 $bonus->addtime=time();
					 $result=$bonus->add(); 
					 if($result){
                         $action="添加登录奖励：{$result}";
                    setAdminLog($action);
                    $this->resetcache();      
						  $this->success('添加成功');
					 }else{
						  $this->error('添加失败');
					 }
				}			
		}		
		function edit(){
			$id=intval($_GET['id']);
			if($id){
				$bonus=M("loginbonus")->where(["id"=>$id])->find();
				$this->assign('bonus', $bonus);						
			}else{				
				$this->error('数据传入失败！');
			}								  
			$this->display();				
		}
		
		function edit_post(){
			if(IS_POST){			
				 $bonus=M("loginbonus");
				 $bonus->create();
				 $bonus->uptime=time();
				 $result=$bonus->save(); 
				 if($result){
                    $action="编辑登录奖励：{$_POST['id']}";
                    setAdminLog($action);
                    $this->resetcache();
                    $this->success('修改成功');
				 }else{
                    $this->error('修改失败');
				 }
			}			
		}
        
        function resetcache(){
            $key='loginbonus';
            $list=M('loginbonus')
                    ->field("day,coin")
                    ->select();
            if($list){
                setcaches($key,$list);
            }
            return 1;
        }
        
        function index2(){
            $Coinrecord=M("users_coinrecord");
            $map['type']='income';
            $map['action']='loginbonus';
            $count=$Coinrecord->where($map)->count();
            $page = $this->page($count, 20);
            $lists = $Coinrecord
                ->where($map)
                ->order("id desc")
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
                
            foreach($lists as $k=>$v){
                $userinfo=getUserInfo($v['uid']);
                $name='第'.$v['giftid'].'天';
                $v['userinfo']=$userinfo;
                $v['name']=$name;
                $lists[$k]=$v;
            }
            $this->assign('lists', $lists);
            $this->assign("page", $page->show('Admin'));
            
            $this->display();
            
        }
		
}
