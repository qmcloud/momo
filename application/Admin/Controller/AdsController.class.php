<?php

/**
 * 广告图片
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class AdsController extends AdminbaseController {
    function index(){

			$ads_sort=M("ads_sort")->getField("id,sortname");
			$ads_sort[0]="默认分类";
			$this->assign('ads_sort', $ads_sort);
			
    	$ads_model=M("ads");
    	$count=$ads_model->count();
    	$page = $this->page($count, 20);
    	$lists = $ads_model
    	->order("orderno asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("ads")->delete($id);				
							if($result){
                                $action="删除广告图片：{$id}";
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
            M("ads")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新广告图片排序";
                    setAdminLog($action);
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    

		function add(){
				$ads_sort=M("ads_sort")->getField("id,sortname");
				$this->assign('ads_sort', $ads_sort);					
			
				$this->display();				
		}	
		function add_post(){
				if(IS_POST){			
					 $ads=M("ads");
					 $ads->create();
					 $ads->addtime=time();
					 $result=$ads->add(); 
					 if($result){
                         $action="添加广告图片：{$result}";
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
						 $ads_sort=M("ads_sort")->getField("id,sortname");
				$this->assign('ads_sort', $ads_sort);		
						$ads=M("ads")->find($id);
						$this->assign('ads', $ads);						
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}
		
		function edit_post(){
				if(IS_POST){			
					 $user=M("ads");
					 $user->create();
					 $result=$user->save(); 
					 if($result){
                         $action="修改广告图片：{$_POST['id']}";
                    setAdminLog($action);
						  $this->success('修改成功');
					 }else{
						  $this->error('修改失败');
					 }
				}			
		}
		
    function sort_index(){
	
    	$ads_sort=M("ads_sort");
    	$count=$ads_sort->count();
    	$page = $this->page($count, 20);
    	$lists = $ads_sort
    	->order("orderno asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }		
		
		function sort_del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("ads_sort")->delete($id);				
							if($result){
                                $action="删除广告图片分类：{$id}";
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
    public function sort_listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("ads_sort")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新广告图片分类排序";
                    setAdminLog($action);
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }				
    function sort_add(){
		    	
    	$this->display();
    }		
		function do_sort_add(){

				if(IS_POST){	
            if($_POST['sortname']==''){
							  $this->error('分类名称不能为空');
							
						}
				
					 $ads_sort=M("ads_sort");
					 $ads_sort->create();
					 $ads_sort->addtime=time();
					 
					 $result=$ads_sort->add(); 
					 if($result){
                         $action="添加广告图片分类：{$result}";
                    setAdminLog($action);
						  $this->success('添加成功');
					 }else{
						  $this->error('添加失败');
					 }
				}				
    }		
    function sort_edit(){

			 	$id=intval($_GET['id']);
					if($id){
						$sort	=M("ads_sort")->find($id);
						$this->assign('sort', $sort);						
					}else{				
						$this->error('数据传入失败！');
					}								      	
    	$this->display();
    }			
		function do_sort_edit(){
				if(IS_POST){			
					 $ads_sort=M("ads_sort");
					 $ads_sort->create();
					 $result=$ads_sort->save(); 
					 if($result){
                         $action="编辑广告图片分类：{$_POST['id']}";
                    setAdminLog($action);
						  $this->success('修改成功');
					 }else{
						  $this->error('修改失败');
					 }
				}	
    }				
}
