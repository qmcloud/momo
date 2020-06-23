<?php

/**
 * 消费记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CoinrecordController extends AdminbaseController {
    function index(){
        
        if($_REQUEST['type']!=''){
              $map['type']=$_REQUEST['type'];
                $_GET['type']=$_REQUEST['type'];
         }
         
         if($_REQUEST['action']!=''){
              $map['action']=$_REQUEST['action'];
                $_GET['action']=$_REQUEST['action'];
         }
         
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
          if($_REQUEST['touid']!=''){
             $map['touid']=$_REQUEST['touid']; 
             $_GET['touid']=$_REQUEST['touid'];
         }

			
    	$coin=M("users_coinrecord");
		$Users=M("users");
		$Gift=M("gift");
		$Vip=M("vip");
		$Car=M("car");
		$Liang=M("liang");
		$Guard=M("guard");
		
    	$count=$coin->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $coin
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				$userinfo=$Users->field("user_nicename")->where("id='$v[uid]'")->find();
				$lists[$k]['userinfo']= $userinfo;
				$touserinfo=$Users->field("user_nicename")->where("id='$v[touid]'")->find();
				$lists[$k]['touserinfo']= $touserinfo;
				$action=$v['action'];
				if($action=='sendgift'){
					$giftinfo=$Gift->field("giftname")->where("id='$v[giftid]'")->find();
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='loginbonus'){
					$giftinfo['giftname']='第'.$v['giftid'].'天';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='sendbarrage'){
					$giftinfo['giftname']='弹幕';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='roomcharge'){
					$giftinfo['giftname']='房间扣费';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='timecharge'){
					$giftinfo['giftname']='计时扣费';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='buyvip'){
					$info=$Vip->field("name")->where("id='{$v[giftid]}'")->find();
					$giftinfo['giftname']=$info['name'];
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='buycar'){
					$info=$Car->field("name")->where("id='{$v[giftid]}'")->find();
					$giftinfo['giftname']=$info['name'];
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='buyliang'){
					$info=$Liang->field("name")->where("id='{$v[giftid]}'")->find();
					$giftinfo['giftname']=$info['name'];
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='sendred'){
					$giftinfo['giftname']='发送红包';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='robred'){
					$giftinfo['giftname']='抢红包';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='buyguard'){
                    $info=$Guard->field("name")->where("id='{$v[giftid]}'")->find();
					$giftinfo['giftname']=$info['name'];
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='reg_reward'){
					$giftinfo['giftname']='注册奖励';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='luckgift'){
					$giftinfo['giftname']='礼物中奖';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='jackpotwin'){
					$giftinfo['giftname']='奖池中奖';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='table_turn'){
					$giftinfo['giftname']='转盘游戏';
					$lists[$k]['giftinfo']= $giftinfo;
				}else if($action=='table_win'){
					$giftinfo['giftname']='转盘中奖';
					$lists[$k]['giftinfo']= $giftinfo;
				}else{
					$giftinfo['giftname']='未知';
					$lists[$k]['giftinfo']= $giftinfo;
				}
				
					 
			}
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("users_coinrecord")->delete($id);				
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

    	
}
