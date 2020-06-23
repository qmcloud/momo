<?php

/**
 * 大转盘 价格配置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class TurntableconController extends AdminbaseController {
    

    function index(){

    	$turntable=M("turntable_con");
    	$lists = $turntable
            ->order("id asc")
            ->select();
            
    	$this->assign('lists', $lists);

    	$this->display();
    }
		
	
		function edit(){
			$id=intval($_GET['id']);
			if($id){
				$data=M("turntable_con")->where(["id"=>$id])->find();
				$this->assign('data', $data);
                
			}else{				
				$this->error('数据传入失败！');
			}								  
			$this->display();				
		}
		
		function edit_post(){
			if(IS_POST){			
				 $turntable=M("turntable_con");
				 $turntable->create();

                 $times=I('times');
                 if($times<1){
                     $this->error('请输入正确的次数');
                 }
                 $coin=I('coin');
                 if($coin<1){
                     $this->error('请输入正确的价格');
                 }
                 
				 $result=$turntable->save(); 
				 if($result!==false){
                    //$action="编辑登录奖励：{$_POST['id']}";
                    //setAdminLog($action);
                    $this->resetcache();
                    $this->success('修改成功');
				 }else{
                    $this->error('修改失败');
				 }
			}			
		}
        
            //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("turntable_con")->where(array('id' => $key))->save($data);
        }
        
        $this->resetcache();
        $this->success("排序更新成功！");

    }	
        
        function resetcache(){
            $key='turntable_con';
            $list=M('turntable_con')
                    ->field("id,times,coin")
                    ->order('orderno asc,id asc')
                    ->select();
            if($list){
                setcaches($key,$list);
            }
            return 1;
        }
}
