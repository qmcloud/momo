<?php

/**
 * 坐骑管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CarController extends AdminbaseController {

    function index(){	
    	$Car=M("car");
    	$count=$Car->count();
    	$page = $this->page($count, 20);
    	$lists = $Car
			->order("orderno asc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
        foreach($lists as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $lists[$k]=$v;
        }
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
	function del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("car")->delete($id);				
				if($result){
                    $action="删除坐骑：{$id}";
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
    //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("car")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新坐骑排序";
            setAdminLog($action);
            $this->resetcache();
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    

	function add(){
			$this->display();				
	}	
	function add_post(){
		if(IS_POST){

			$name=$_POST['name'];

			if($name==""){
				$this->error("请填写坐骑名称");
			}
			$needcoin=$_POST['needcoin'];
			if($needcoin==""){
				$this->error("请填写坐骑所需点数");
			}

			if(!is_numeric($needcoin)){
				$this->error("请确认坐骑所需点数");
			}

			$swftime=$_POST['swftime'];
			if($swftime==""){
				$this->error("请填写动画时长");
			}

			if(!is_numeric($swftime)){
				$this->error("请确认动画时长");
			}

			$words=$_POST['words'];
			if($words==""){
				$this->error("请填写进场话术");
			}

						
			$Car=M("car");
			 $Car->create();
			 $Car->addtime=time();
			 $result=$Car->add(); 
			 if($result!==false){
                 $action="添加坐骑：{$result}";
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
			$car=M("car")->find($id);
			$this->assign('car', $car);		
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function edit_post(){
		if(IS_POST){	
			$Car=M("car");
			$Car->create();
			$result=$Car->save(); 
			if($result!==false){
                $action="修改坐骑：{$_POST['id']}";
                setAdminLog($action);
                $this->resetcache();
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
    	->order("endtime asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
		foreach($lists as $k=>$v){
			$lists[$k]['userinfo']=getUserInfo($v['uid']);
		}
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign('type', $this->type);    	
    	$this->display();
    }		
		
	function user_del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("users_vip")->delete($id);				
			if($result){
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
		$this->assign('type', $this->type);    	
    	$this->display();
    }		
	function do_user_add(){

		if(IS_POST){	
			$uid=(int)$_POST['uid'];
            if($uid==''){
				$this->error('用户ID不能为空');
			}
			$isexist=M("users")->field("id")->where(["id"=>$uid])->find();
			if(!$isexist){
				$this->error('该用户不存在');
			}
			
			$Vip_u=M("users_vip");
			$isexist2=$Vip_u->field("id")->where(["uid"=>$uid])->find();
			if($isexist2){
				$this->error('该用户已购买过会员');
			}
			
			$Vip_u->create();
			$Vip_u->addtime=time();
			$Vip_u->endtime=strtotime($_POST['endtime']);
			$result=$Vip_u->add(); 
			if($result!==false){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}				
    }		
    function user_edit(){

		$id=intval($_GET['id']);
		if($id){
			$data	=M("users_vip")->where(["id"=>$id])->find();
			$data['userinfo']=getUserInfo($data['uid']);
			$this->assign('data', $data);	
			$this->assign('type', $this->type);
		}else{				
			$this->error('数据传入失败！');
		}								      	
    	$this->display();
    }			
	function do_user_edit(){
		if(IS_POST){			
			$Vip_u=M("users_vip");
			$Vip_u->create();
			$Vip_u->endtime=strtotime($_POST['endtime']);
			$result=$Vip_u->save(); 
			if($result!==false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}	
    }
    
    function resetcache(){
        $key='carinfo';

        $car_list=M("car")->order("orderno asc")->select();
        if($car_list){
            setcaches($key,$car_list);
        }    
        return 1;
    }
}
