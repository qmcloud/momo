<?php

/**
 * 充值规则
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ChargerulesController extends AdminbaseController {

		
    function index(){
	
    	$rules=M("charge_rules");
    	$count=$rules->count();
    	$page = $this->page($count, 20);
    	$lists = $rules
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
			$result=M("charge_rules")->where(["id"=>$id])->delete();				
				if($result){
                    $action="删除充值规则：{$id}";
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
            M("charge_rules")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新充值规则排序";
            setAdminLog($action);
            $this->resetCache();
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
	
    function add(){
		$this->display();
    }	
	
	function do_add(){
		if(IS_POST){	
			$rules=M("charge_rules");
			$rules->create();
			$rules->addtime=time();
			 
			$result=$rules->add(); 
			if($result){
                $action="添加充值规则：{$result}";
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
			$rules	=M("charge_rules")->where(["id"=>$id])->find();
			$this->assign('rules', $rules);						
		}else{				
			$this->error('数据传入失败！');
		}								      	
    	$this->display();
    }		
	
	function do_edit(){
		if(IS_POST){			
			 $rules=M("charge_rules");
			 $rules->create();
			 $result=$rules->save(); 
			 if($result){
                 $action="修改充值规则：{$_POST['id']}";
                    setAdminLog($action);
                    $this->resetCache();   
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }
		}	
    }	

    function resetCache(){
        $key='getChargeRules';
        $rules= M("charge_rules")
            ->field('id,coin,coin_ios,money,money_ios,product_id,give')
            ->order('orderno asc')
            ->select();
        setcaches($key,$rules);
        return 1;
    }
}
