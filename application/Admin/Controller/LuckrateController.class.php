<?php

/**
 * 幸运礼物中奖设置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LuckrateController extends AdminbaseController {
    var $numslist=['1','10','66','88','100','520','1314'];
    function index(){
        $giftid=(int)I('giftid');
        $map['giftid']=$giftid;
        
        $giftinfo=M('gift')
            ->field('giftname')
            ->where(["id"=>$giftid])
            ->find();
        
    	$jackpot=M("luck_rate");
    	$count=$jackpot->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $jackpot
            ->where($map)
            ->order("id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

    	$this->assign('lists', $lists);
    	$this->assign('giftid', $giftid);
    	$this->assign('giftinfo', $giftinfo);

    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
   
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("luck_rate")->delete($id);				
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
        $giftid=(int)I('giftid');
        $this->assign('giftid', $giftid);
        $this->assign('numslist', $this->numslist);
        $this->display();				
    }
    
    function add_post(){
        if(IS_POST){
            $jackpot=M("luck_rate");
            $jackpot->create();
            
            $giftid=(int)I('giftid');
            $nums=(int)I('nums');
            $times=(int)I('times');

            
            if($times < 0){
				$this->error('中奖倍数不能小于0');
			}
            $where['giftid']=$giftid;
            $where['nums']=$nums;
            $where['times']=$times;
            
            $check = $jackpot->where($where)->find();
            if($check){
                $this->error('相同数量、倍数的配置已存在');
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
            $data=M("luck_rate")->find($id);
            
            
            $this->assign('numslist', $this->numslist);
            $this->assign('data', $data);						
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }
    
    function edit_post(){
        if(IS_POST){			
             $jackpot=M("luck_rate");
             $jackpot->create();
             
            $id=(int)I('id');
            $giftid=(int)I('giftid');
            $nums=(int)I('nums');
            $times=(int)I('times');

            if($times < 0){
				$this->error('中奖倍数不能小于0');
			}
            
            $where['giftid']=$giftid;
            $where['nums']=$nums;
            $where['times']=$times;
            $where['id']=array('neq',$id);
            
            $check = $jackpot->where($where)->find();
            if($check){
                $this->error('相同数量、倍数的配置已存在');
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
		$key='luck_rate';

        $level= M("luck_rate")->order("id desc")->select();
        if($level){
            setcaches($key,$level);
        }
       
        return 1;
    }       

}
