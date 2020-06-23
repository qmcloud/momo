<?php

/**
 * 直播记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LiveingController extends AdminbaseController {
    function index(){
			$config=getConfigPub();
			$map=array();
			$map['islive']=1;
		   if($_REQUEST['start_time']!=''){
				  $map['starttime']=array("gt",strtotime($_REQUEST['start_time']));
					$_GET['start_time']=$_REQUEST['start_time'];
			 }
			 
			 if($_REQUEST['end_time']!=''){
				 
				   $map['starttime']=array("lt",strtotime($_REQUEST['end_time']));
					 $_GET['end_time']=$_REQUEST['end_time'];
			 }
			 if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
				 
				 $map['starttime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
				 $_GET['start_time']=$_REQUEST['start_time'];
				 $_GET['end_time']=$_REQUEST['end_time'];
			 }

			 if($_REQUEST['keyword']!=''){
				 $map['uid']=$_REQUEST['keyword']; 
				 $_GET['keyword']=$_REQUEST['keyword'];
			 }
			
	
			
    	$live=M("users_live");
    	$Coinrecord=M("users_coinrecord");
    	$count=$live->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $live
    	->where($map)
    	->order("starttime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			$redis=connectionRedis();
			foreach($lists as $k=>$v){
				 $userinfo=getUserInfo($v['uid']);
				 $v['userinfo']=$userinfo;
                 $where=[];
                 $where['action']='sendgift';
                 $where['touid']=$v['uid'];
                 $where['showid']=$v['showid'];
                 /* 本场总收益 */
                 $totalcoin=$Coinrecord->where($where)->sum('totalcoin');
                 if(!$totalcoin){
                    $totalcoin=0;
                 }
                 /* 送礼物总人数 */
                 $total_nums=$Coinrecord->where($where)->group("uid")->count();
                 if(!$total_nums){
                    $total_nums=0;
                 }
                 /* 人均 */
                 $total_average=0;
                 if($totalcoin && $total_nums){
                    $total_average=round($totalcoin/$total_nums,2);
                 }
                 
                 /* 人数 */
                $nums=$redis->zSize('user_'.$v['stream']);
                
                $v['totalcoin']=$totalcoin;
                $v['total_nums']=$total_nums;
                $v['total_average']=$total_average;
                $v['nums']=$nums;
                
                if($v['isvideo']==0 && $configpri['cdn_switch']!=5){
                    $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
                }
                
                $lists[$k]=$v;
			}
			
    	$this->assign('config', $config);
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
        $liveclass=M("live_class")->getfield('id,name');
        $liveclass[0]='默认分类';
        $this->assign('liveclass', $liveclass);
        
    	$this->display();
    }
	
	function del(){
		$uid=intval(I('uid'));
		if($uid){
			$result=M("users_live")->where(["uid"=>$uid])->delete();				
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

    
	function add(){
        
        $liveclass=M("live_class")->order('orderno asc,id desc')->select();
        $this->assign('liveclass', $liveclass);
        
        $this->display();				
	}	
	function add_post(){
		if(IS_POST){	
			$nowtime=time();
			$uid=(int)$_POST['uid'];
			$pull=urldecode($_POST['pull']);
			$type=$_POST['type'];
			$type_val=$_POST['type_val'];
			$anyway=$_POST['anyway'];
			$liveclassid=I('liveclassid');
			
			
			$User=M("users");
			$live=M("users_live");
			
			$userinfo=$User->field("ishot,isrecommend")->where(["id"=>$uid])->find();
			if(!$userinfo){
				$this->error('用户不存在');
			}
			
			$liveinfo=$live->field('uid,islive')->where(["uid"=>$uid])->find();
			if($liveinfo['islive']==1){
				$this->error('该用户正在直播');
			}
			
			$title='';
			$stream=$uid.'_'.$nowtime;
			$data=array(
				"uid"=>$uid,
				"ishot"=>$userinfo['ishot'],
				"isrecommend"=>$userinfo['isrecommend'],
                
				"showid"=>$nowtime,
				"starttime"=>$nowtime,
				"title"=>$title,
				"province"=>'',
				"city"=>'好像在火星',
				"stream"=>$stream,
				"thumb"=>'',
				"pull"=>$pull,
				"lng"=>'',
				"lat"=>'',
				"type"=>$type,
				"type_val"=>$type_val,
				"isvideo"=>1,
				"islive"=>1,
				"anyway"=>$anyway,
				"liveclassid"=>$liveclassid,
			);	
		
			 if($liveinfo){
				$result=$live->where(["uid"=>$uid])->save($data); 
			 }else{
				$result=$live->add($data); 
			 }
			 
			 if($result!==false){
				  $this->success('添加成功');
			 }else{
				  $this->error('添加失败');
			 }
		}			
	}		
	function edit(){
		$uid=intval($_GET['uid']);
		if($uid){
			$live=M("users_live")->where("uid={$uid}")->find();
            
            $liveclass=M("live_class")->order('orderno asc,id desc')->select();
            $this->assign('liveclass', $liveclass);
        
        
			$this->assign('live', $live);						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function edit_post(){
		if(IS_POST){	
            $pull=I('pull');
			 $live=M("users_live");
			 $live->create();
             $live->pull=urldecode($pull);
			 $result=$live->save(); 
			 if($result!==false){
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }
		}			
	}
		
}
