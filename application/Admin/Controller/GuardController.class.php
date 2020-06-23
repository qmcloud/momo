<?php

/**
 * 守护
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GuardController extends AdminbaseController {

    var $type_a=array(
        '1'=>'普通守护',
        '2'=>'尊贵守护',
    );
    var $length_type_a=array(
        '0'=>'天',
        '1'=>'月',
        '2'=>'年',
    );
    
    var $length_time_a=array(
        '0'=>60*60*24,
        '1'=>60*60*24*30,
        '2'=>60*60*24*365,
    );
    function index(){
	
    	$rules=M("guard");
    	$count=$rules->count();
    	$page = $this->page($count, 20);
    	$lists = $rules
				->order("orderno asc")
				->limit($page->firstRow . ',' . $page->listRows)
				->select();
    	$this->assign('lists', $lists);
    	$this->assign('type_a', $this->type_a);
    	$this->assign('length_type_a', $this->length_type_a);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }		
		
	function del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("guard")->where(["id"=>$id])->delete();				
				if($result){
                    $action="删除守护：{$id}";
                    setAdminLog($action);
                    $this->resetCache();

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
            M("guard")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新守护排序";
            setAdminLog($action);
            $this->resetCache();
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
	
    function add(){
        $this->assign('type_a', $this->type_a);
    	$this->assign('length_type_a', $this->length_type_a);
		$this->display();
    }	
	
	function do_add(){
		if(IS_POST){	
            $name=I('name');
            $coin=I('coin');
            $length=I('length');
            $length_type=I('length_type');
            if($name==''){
                $this->error('请输入名称');
            }
            if($coin=='' || (int)$coin<1){
                $this->error('请输入有效价格');
            }
            if($length=='' || (int)$length<1){
                $this->error('请输入有效时长');
            }
            
			$guard=M("guard");
			$guard->create();
            
			$guard->length_time=$length * $this->length_time_a[$length_type];
			$guard->addtime=time();
			$guard->uptime=time();
			 
			$result=$guard->add(); 
			if($result){
                $action="添加守护：{$result}";
                setAdminLog($action);
                $this->resetCache();
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}				
    }		
    function edit(){
		$id=intval($_GET['id']);
		if($id){
			$data	=M("guard")->where(["id"=>$id])->find();
			$this->assign('data', $data);		

            $this->assign('type_a', $this->type_a);
            $this->assign('length_type_a', $this->length_type_a);
		}else{				
			$this->error('数据传入失败！');
		}								      	
    	$this->display();
    }		
	
	function do_edit(){
		if(IS_POST){	
            $name=I('name');
            $coin=I('coin');
            $length=I('length');
            $length_type=I('length_type');
            if($name==''){
                $this->error('请输入名称');
            }
            if($coin=='' || (int)$coin<1){
                $this->error('请输入有效价格');
            }
            if($length=='' || (int)$length<1){
                $this->error('请输入有效时长');
            }
            
			 $guard=M("guard");
			 $guard->create();
             $guard->length_time=$length * $this->length_time_a[$length_type];
             $guard->uptime=time();
			 $result=$guard->save(); 
			 if($result){
                 $action="修改守护：{$_POST['id']}";
                    setAdminLog($action);
                    $this->resetCache();   
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }
		}	
    }	

    function resetCache(){
        $key='guard_list';
        $list= M("guard")
            ->field('id,name,type,coin')
            ->order('orderno asc')
            ->select();
        setcaches($key,$list);
        return 1;
    }
}
