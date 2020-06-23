<?php

/**
 * 经验等级
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LevelController extends AdminbaseController {
	
		protected $experlevel_model;
	
		function _initialize() {
			parent::_initialize();
			$this->experlevel_model = D("Common/Experlevel");

		}	
	
    function experlevel_index(){			
    	$experlevel=$this->experlevel_model;
    	$count=$experlevel->count();
    	$page = $this->page($count, 20);
    	$lists = $experlevel
            ->order("levelid asc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach($lists as $k=>$v){
            $lists[$k]['thumb']=get_upload_path($v['thumb']);
            $lists[$k]['thumb_mark']=get_upload_path($v['thumb_mark']);
            $lists[$k]['bg']=get_upload_path($v['bg']);
        }
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
	function experlevel_del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("experlevel")->where(["id"=>$id])->delete();				
			if($result!==false){
                $action="删除会员等级：{$id}";
                    setAdminLog($action);
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



	function experlevel_add(){
		$this->display();				
	}	
	function experlevel_add_post(){
		if(IS_POST){
            $levelid=$_POST['levelid'];
			if($levelid == ''){
				$this->error('等级不能为空');
			}else{
				$check = M('experlevel')->where(["levelid"=>$levelid])->find();
				if($check){
					$this->error('等级不能重复');
				}
			}
            
            $level_up=I('level_up');
            $colour=I('colour');
            $thumb=I('thumb');
            $thumb_mark=I('thumb_mark');
            if($level_up==''){
                $this->error('请填写等级经验上限');
            }
            if($colour==''){
                $this->error('请填写昵称颜色');
            }
            if($thumb==''){
                $this->error('请上传图标');
            }
            if($thumb_mark==''){
                $this->error('请上传头像角标');
            } 
            
			$experlevel=$this->experlevel_model;
			if($experlevel->create()){
				$experlevel->addtime=time();
				$result=$experlevel->add(); 
				if($result!==false){
                    $action="添加会员等级：{$result}";
                    setAdminLog($action);
                    $this->resetcache();
					$this->success('添加成功');
				}else{
					$this->error('添加失败');
				}						 
				 
			}else{
				$this->error($this->experlevel_model->getError());
			}
		}			
	}		
	function experlevel_edit(){
		$id=intval($_GET['id']);
		if($id){
			$experlevel=M("experlevel")->where(["id"=>$id])->find();
			$this->assign('experlevel', $experlevel);						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function experlevel_edit_post(){
		if(IS_POST){
			$id=(int)$_POST['id'];
			$levelid=$_POST['levelid'];
			if($levelid == ''){
				$this->error('等级不能为空');
			}else{
                $where['levelid']=$levelid;
                $where['id']=array('neq',$id);
				$check = M('experlevel')->where($where)->find();
				if($check){
					$this->error('等级不能重复');
				}
			}
            
            $level_up=I('level_up');
            $colour=I('colour');
            $thumb=I('thumb');
            $thumb_mark=I('thumb_mark');
            if($level_up==''){
                $this->error('请填写等级经验上限');
            }
            if($colour==''){
                $this->error('请填写昵称颜色');
            }
            if($thumb==''){
                $this->error('请上传图标');
            }
            if($thumb_mark==''){
                $this->error('请上传头像角标');
            } 
            
            $experlevel=M("experlevel");
			$experlevel->create();
				
			$result=$experlevel->save(); 

            if($result!==false){
                $action="修改会员等级：{$POST['id']}";
                setAdminLog($action);
                $this->resetcache();
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }					 

        }
			
	}
    
    function resetcache(){
		$key='level';


        $level= M("experlevel")->order("level_up asc")->select();
        
        foreach($level as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $v['thumb_mark']=get_upload_path($v['thumb_mark']);
            $v['bg']=get_upload_path($v['bg']);
            if($v['colour']){
                $v['colour']='#'.$v['colour'];
            }else{
                $v['colour']='#ffdd00';
            }
            $level[$k]=$v;
        }
            
        setcaches($key,$level);			 


        return 1;
    }
		
}
