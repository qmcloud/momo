<?php

/**
 * 奖池中奖设置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class JackpotrateController extends AdminbaseController {
    var $numslist=['1','10','66','88','100','520','1314'];
    function index(){
        $giftid=(int)I('giftid');
        $map['giftid']=$giftid;
        
        $giftinfo=M('gift')
            ->field('giftname')
            ->where(["id"=>$giftid])
            ->find();
        
    	$jackpot=M("jackpot_rate");
    	$count=$jackpot->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $jackpot
            ->where($map)
            ->order("id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach($lists as $k=>$v){
            $v['rate_jackpot']=json_decode($v['rate_jackpot'],true);
            $lists[$k]=$v;
        }
        
        $this->assign('jackpot_level', $this->getJackpotLevel());
    	$this->assign('lists', $lists);
    	$this->assign('giftid', $giftid);
    	$this->assign('giftinfo', $giftinfo);

    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
    
    function getJackpotLevel(){
        $jackpot=M("jackpot_level");
    	$lists = $jackpot
            ->order("levelid asc")
            ->select();
            
        return $lists;
        
    }
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("jackpot_rate")->delete($id);				
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
        $giftid=I('giftid');
        $this->assign('jackpot_level', $this->getJackpotLevel());
        $this->assign('giftid', $giftid);
        $this->assign('numslist', $this->numslist);
        $this->display();				
    }
    
    function add_post(){
        if(IS_POST){
            $jackpot=M("jackpot_rate");
            $jackpot->create();
            
            $giftid=(int)I('giftid');
            $nums=(int)I('nums');

            $rate_jackpot=$_POST['rate_jackpot'];
            
            $where['giftid']=$giftid;
            $where['nums']=$nums;
            
            $check = $jackpot->where($where)->find();
            if($check){
                $this->error('相同数量的配置已存在');
            }

             $jackpot->rate_jackpot=json_encode($rate_jackpot);
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
            $data=M("jackpot_rate")->find($id);
            
            $data['rate_jackpot']=json_decode($data['rate_jackpot'],true);
            
            $this->assign('jackpot_level', $this->getJackpotLevel());
            $this->assign('numslist', $this->numslist);
            $this->assign('data', $data);						
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }
    
    function edit_post(){
        if(IS_POST){			
             $jackpot=M("jackpot_rate");
             $jackpot->create();
             
            $id=(int)I('id');
            $giftid=(int)I('giftid');
            $nums=(int)I('nums');

            $rate_jackpot=$_POST['rate_jackpot'];
            
            $where['giftid']=$giftid;
            $where['nums']=$nums;
            $where['id']=['neq',$id];
            
            $check = $jackpot->where($where)->find();
            if($check){
                $this->error('相同数量的配置已存在');
            }

            $jackpot->rate_jackpot=json_encode($rate_jackpot);
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
		$key='jackpot_rate';

        $level= M("jackpot_rate")->order("id desc")->select();
        if($level){
            setcaches($key,$level);
        }
       
        return 1;
    }       

}
