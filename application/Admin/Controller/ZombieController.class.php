<?php

/**
 * 僵尸粉
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ZombieController extends AdminbaseController {
	
    function index(){
			
			$map=array();
			$map['user_type']=2;
			
				 
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
				$where['id|user_login|user_nicename']	=array("like","%".$_REQUEST['keyword']."%");
				$where['_logic']	="or";
				$map['_complex']=$where;
				
				$_GET['keyword']=$_REQUEST['keyword'];
			 }
			

    	$users_model=M("users_zombie");
    	$count=$users_model->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $users_model
    	->where($map)
    	->order("create_time DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();

			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show("Admin"));
    	
    	$this->display();
    }
     function del(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("users_zombie")->where(array("id"=>$id,"user_type"=>2))->delete();
    		if ($rst!==false) {
    			$this->success("僵尸粉删除成功！");
    		} else {
    			$this->error('僵尸粉删除失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }   

  
    
  
	function add(){
		$this->display();				
	}
	
	function add_post(){
		if(IS_POST){			
			$user=M("users_zombie");
			 
			if( $user->create()){
				 $user->user_type=2;
				$user->user_status=1;
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
					$user->avatar=  $avatar.'?imageView2/2/w/600/h/600'; //600 X 600
					$user->avatar_thumb=  $avatar.'?imageView2/2/w/200/h/200'; // 200 X 200
				}
				$user->last_login_time=date('y-m-d h:i:s',time());
				$user->create_time=date('y-m-d h:i:s',time());
				
				 $result=$user->add(); 
				 if($result!==false){
						$this->success('添加成功');
				 }else{
						$this->error('添加失败');
				 }					 
			 }else{
				  $this->error();
			 }
		}			
	}		
	function edit(){
			$id=intval($_GET['id']);
				if($id){
					$userinfo=M("users_zombie")->find($id);
					$this->assign('userinfo', $userinfo);						
				}else{				
					$this->error('数据传入失败！');
				}								  
				$this->display();				
	}
	
	function edit_post(){
		if(IS_POST){			
			 $user=M("users_zombie");
			$user->create();
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
				$user->avatar=  $avatar.'?imageView2/2/w/600/h/600'; //600 X 600
				$user->avatar_thumb=  $avatar.'?imageView2/2/w/200/h/200'; // 200 X 200
			}
			 $result=$user->save(); 
			 if($result!==false){
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }
		}			
	}

}
