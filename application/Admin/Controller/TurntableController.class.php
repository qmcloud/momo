<?php

/**
 * 大转盘
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class TurntableController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=[
            '0'=>'无奖',
            '1'=>'钻石',
            '2'=>'礼物',
            '3'=>'线下奖品',
        ];
        
        if($k==''){
            return $type;
        }
        
        return $type[$k];
    }
    
    function index(){

    	$turntable=M("turntable");
    	$lists = $turntable
            ->order("id asc")
            ->select();
            
        foreach($lists as $k=>$v){
            $name='无奖品';
            
             if($v['type']==1){
                $name=$v['type_val'];
            }
            
            if($v['type']==2){
                $name='已删除';
                $giftinfo=M("gift")->field('giftname')->where("id={$v['type_val']}")->find();
                if($giftinfo){
                    $name=$giftinfo['giftname'];
                }
            }
            
            if($v['type']==3){
                $name=$v['type_val'];
            }
            
            $v['name']=$name;
            
            $lists[$k]=$v;
        }
        
    	$this->assign('lists', $lists);
        
        
    	$this->assign('type', $this->getTypes());

    	$this->display();
    }
		
	
    function edit(){
        $id=intval($_GET['id']);
        if($id){
            $data=M("turntable")->where(["id"=>$id])->find();
            $this->assign('data', $data);
            
            $gift=M('gift')->field('id,giftname')->order('orderno desc')->select();
            
            $this->assign('gift', $gift);
            
            $this->assign('type', $this->getTypes());
            
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }
    
    function edit_post(){
        if(IS_POST){			
             $turntable=M("turntable");
             $turntable->create();
             $turntable->uptime=time();
             $type=I('type');
             if($type==1){
                 $type_val=intval(I('coin'));
                 if($type_val<1){
                     $this->error('请输入正确的钻石数');
                 }
                 $turntable->type_val=$type_val;
             }
             
             if($type==2){
                 $turntable->type_val=intval(I('giftid'));
                 $type_val=intval(I('coin'));
                 if($type_val<1){
                     $this->error('请输入正确的钻石数');
                 }
                 $turntable->type_val=$type_val;
             }
             
             if($type==3){                     
                 $type_val=I('name');
                 if($type_val==''){
                     $this->error('请输入奖品名');
                 }
                 $turntable->type_val=$type_val;
                 
                 $thumb=I('thumb');
                 if($thumb==''){
                     $this->error('请上传奖品图片');
                 }
                 $turntable->thumb=$thumb;
                 
             }
             
             $result=$turntable->save(); 
             if($result){
                //$action="编辑登录奖励：{$_POST['id']}";
                //setAdminLog($action);
                $this->resetcache();
                $this->success('修改成功');
             }else{
                $this->error('修改失败');
             }
        }			
    }
    
    function resetcache(){
        $key='turntable';
        $list=M('turntable')
                ->field("id,type,type_val,thumb,rate")
                ->select();
        if($list){
            setcaches($key,$list);
        }
        return 1;
    }
    
    function index2(){
        
        $map=array();
        

        if($_REQUEST['start_time']!=''){
            $map['addtime']=array("gt",strtotime($_REQUEST['start_time']));
            $_GET['start_time']=$_REQUEST['start_time'];
        }
         
        if($_REQUEST['end_time']!=''){
            $map['addtime']=array("lt",strtotime($_REQUEST['end_time']));
            $_GET['end_time']=$_REQUEST['end_time'];
        }
        if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
            $map['addtime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
            $_GET['start_time']=$_REQUEST['start_time'];
            $_GET['end_time']=$_REQUEST['end_time'];
        }
         
        if($_REQUEST['uid']!=''){
            $map['uid']=$_REQUEST['uid']; 
            $_GET['uid']=$_REQUEST['uid'];
        }
        
        if($_REQUEST['liveuid']!=''){
            $map['liveuid']=$_REQUEST['liveuid']; 
            $_GET['liveuid']=$_REQUEST['liveuid'];
        }
        
        if($_REQUEST['showid']!=''){
            $map['showid']=$_REQUEST['showid']; 
            $_GET['showid']=$_REQUEST['showid'];
        }
    
    
        $log=M("turntable_log");
        $win=M("turntable_win");

        $count=$log->where($map)->count();
        $total=$log->where($map)->sum('coin');
        if(!$total){
            $total=0;
        }
        $page = $this->page($count, 20);
        $lists = $log
            ->where($map)
            ->order("id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
            
        foreach($lists as $k=>$v){

            $userinfo=getUserInfo($v['uid']);
            $v['userinfo']=$userinfo;
            $liveuidinfo=getUserInfo($v['liveuid']);
            $v['liveuidinfo']=$liveuidinfo;
            
            $winlist=[];
            if($v['iswin']==1){
                $winlist=$win->where("logid={$v['id']}")->select();
                
                foreach($winlist as $k2=>$v2){
                    
                    if($v2['type']==3){
                        $name=$v2['type_val'];
                    }
                    
                    if($v2['type']==2){
                        $name='已删除';
                        $giftinfo=M("gift")->field('giftname')->where("id={$v2['type_val']}")->find();
                        if($giftinfo){
                            $name=$giftinfo['giftname'];
                        }
                    }
                    
                    if($v2['type']==1){
                        $name=$v2['type_val'];
                    }
                    
                    $v2['name']=$name;
                    $winlist[$k2]=$v2;
                }
            }
            
            $v['winlist']=$winlist;
            
            $lists[$k]=$v;
        }
        
        
        $this->assign('lists', $lists);
        $this->assign('formget', $_GET);
        $this->assign('count', $count);
        $this->assign('total', $total);
        $this->assign("page", $page->show('Admin'));
        $this->assign('type', $this->getTypes());
        
        $this->display();
        
    }
        
    function index3(){
        
        $map=array();
        
        $map['type']=3;
        if($_REQUEST['uid']!=''){
            $map['uid']=$_REQUEST['uid']; 
            $_GET['uid']=$_REQUEST['uid'];
        }
    
    
        $log=M("turntable_win");

        $count=$log->where($map)->count();
        $page = $this->page($count, 20);
        $lists = $log
            ->where($map)
            ->order("id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
            
        foreach($lists as $k=>$v){
            
            $userinfo=getUserInfo($v['uid']);
            
            $v['userinfo']=$userinfo;
            $lists[$k]=$v;
        }
        $this->assign('lists', $lists);
        $this->assign("page", $page->show('Admin'));
        $this->assign('type', $this->getTypes());
        
        $this->display();
        
    }
        
    function setStatus(){
        $id=intval($_GET['id']);
        $status=intval($_GET['status']);
        if($id){
            $result=M("turntable_win")->where("id='{$id}'")->save(['status'=>$status,'uptime'=>time()]);

            $this->success('操作成功');

        }else{				
            $this->error('数据传入失败！');
        }
    }
		
}
