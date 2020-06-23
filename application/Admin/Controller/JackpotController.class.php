<?php

/**
 * 奖池设置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class JackpotController extends AdminbaseController {

    function set(){

        $config=M("options")->where("option_name='jackpot'")->getField("option_value");

		$this->assign('config',json_decode($config,true) );
    	
    	$this->display();
    }
    
    function set_post(){

        if(IS_POST){
			
			$config=I("post.post");

            if ( M("options")->where("option_name='jackpot'")->save(['option_value'=>json_encode($config)] )!==false) {
                $key='jackpotset';
                setcaches($key,$config);
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
		}
    }
    
    function index(){
   
    	$jackpot=M("jackpot_level");
    	$count=$jackpot->count();
    	$page = $this->page($count, 20);
    	$lists = $jackpot
            ->order("levelid asc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
            
    	$this->assign('lists', $lists);

    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("jackpot_level")->delete($id);				
                if($result){
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
            $jackpot=M("jackpot_level");
            $jackpot->create();
            $levelid=$_POST['levelid'];
            if($levelid == ''){
				$this->error('等级不能为空');
			}else{
				$check = $jackpot->where(["levelid"=>$levelid])->find();
				if($check){
					$this->error('等级不能重复');
				}
			}
            
            $level_up=I('level_up');

            if($level_up==''){
                $this->error('请填写等级下限');
            }

             $jackpot->addtime=time();
             $result=$jackpot->add(); 
             if($result){
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
            $data=M("jackpot_level")->find($id);
            $this->assign('data', $data);						
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }
    
    function edit_post(){
        if(IS_POST){			
             $jackpot=M("jackpot_level");
             $jackpot->create();
             $id=(int)$_POST['id'];
             $levelid=$_POST['levelid'];
             
            if($levelid == ''){
				$this->error('等级不能为空');
			}else{
                $where['levelid']=$levelid;
                $where['id']=['neq',$id];
				$check = $jackpot->where($where)->find();
				if($check){
					$this->error('等级不能重复');
				}
			}
            
            $level_up=I('level_up');

            if($level_up==''){
                $this->error('请填写等级下限');
            }

            
             $result=$jackpot->save(); 
             if($result!==false){
                 $this->resetcache();
                  $this->success('修改成功');
             }else{
                  $this->error('修改失败');
             }
        }			
    }
     function resetcache(){
		$key='jackpot_level';

        $level= M("jackpot_level")->order("level_up asc")->select();
        if($level){
            setcaches($key,$level);
        }
        
        return 1;
    }       

}
