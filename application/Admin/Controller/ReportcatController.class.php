<?php

/**
 * 用户举报类型
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ReportcatController extends AdminbaseController {
    function index(){

         $map=[];
         if($_REQUEST['keyword']!=''){
             $map['name']=array("like","%".$_REQUEST['keyword']."%"); 
             $_GET['keyword']=$_REQUEST['keyword'];
         }		
			
    	$Report=M("users_report_classify");
    	$count=$Report->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $Report
    	->where($map)
    	->order("orderno asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();

    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
	  //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("users_report_classify")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }			

		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("users_report_classify")->delete($id);				
							if($result){
                                $action="删除用户举报类型：{$id}";
                                setAdminLog($action);
									$this->success('删除成功');
							 }else{
									$this->error('删除失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  
		}		

		
		function add(){
            $this->display();				
		}
		
		function add_post(){
				if(IS_POST){		
                    $report=M("users_report_classify");
                    
                    $name=I("name");//举报类型名称
                    if(!trim($name)){
                        $this->error('举报类型名称不能为空');
                    }
                    $isexit=$report->where(["name"=>$name])->find();	
                    if($isexit){
                        $this->error('该举报类型名称已存在');
                    }
                    
                    $report->create();
                    $report->addtime=time();
                    $result=$report->add(); 
                    if($result){
                        $this->success('添加成功');
                    }else{
                        $this->error('添加失败');
                    }
				}			
		}		

		function edit(){
			 	$id=intval($_GET['id']);
					if($id){
						$reportinfo=M("users_report_classify")->where(["id"=>$id])->find();
			
                        $this->assign('reportinfo', $reportinfo);						
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}
		
		function edit_post(){
				if(IS_POST){		
                    $report=M("users_report_classify");
                    
                    $id=(int)I("id");
                    $name=I("name");//举报类型名称
                    if(!trim($name)){
                        $this->error('举报类型名称不能为空');
                    }
                
                    $where['id']=array('neq',$id);
                    $where['name']=$name;
                    $isexit=$report->where($where)->find();	
                    if($isexit){
                        $this->error('该举报类型名称已存在');
                    }
                    
                    $report->create();
                    $result=$report->save(); 
                    if($result!==false){
                          $this->success('修改成功');
                     }else{
                          $this->error('修改失败');
                     }
				}			
		}		
    
}
