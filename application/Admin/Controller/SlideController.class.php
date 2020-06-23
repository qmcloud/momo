<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SlideController extends AdminbaseController{
	
	protected $slide_model;
	protected $slidecat_model;
	
	function _initialize() {
		parent::_initialize();
		$this->slide_model = D("Common/Slide");
		$this->slidecat_model = D("Common/SlideCat");
		
	}
	
	function index(){
		$cates=array(
				array("cid"=>"0","cat_name"=>"默认分类"),
		);
		$categorys=$this->slidecat_model->field("cid,cat_name")->where("cat_status!=0")->select();
		if($categorys){
			$categorys=array_merge($cates,$categorys);
		}else{
			$categorys=$cates;
		}
		$this->assign("categorys",$categorys);
		$where=[];
		$cid=0;
		if(isset($_POST['cid']) && $_POST['cid']!=""){
			$cid=(int)$_POST['cid'];
			$where['slide_cid']=$cid;
		}
		$this->assign("slide_cid",$cid);
		$slides=$this->slide_model->where($where)->order("listorder ASC")->select();
		$this->assign('slides',$slides);
		$this->display();
	}
	
	function add(){
		$categorys=$this->slidecat_model->field("cid,cat_name")->where("cat_status!=0")->select();
		$this->assign("categorys",$categorys);
		$this->display();
	}
	
	function add_post(){
		if(IS_POST){
			if ($this->slide_model->create()) {
				$_POST['slide_pic']=sp_asset_relative_url($_POST['slide_pic']);
				$this->slide_model->slide_url=$_POST['slide_url'];
				if ($this->slide_model->add()!==false) {
                    $action="添加轮播";
                    setAdminLog($action);
                    $this->resetcache();
					$this->success("添加成功！", U("slide/index"));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error($this->slide_model->getError());
			}
		}
	}
	
	function edit(){
		$categorys=$this->slidecat_model->field("cid,cat_name")->where("cat_status!=0")->select();
		$id= intval(I("get.id"));
		$slide=$this->slide_model->where("slide_id={$id}")->find();
		$this->assign($slide);
		$this->assign("categorys",$categorys);
		$this->display();
	}
	
	function edit_post(){
		if(IS_POST){
			if ($this->slide_model->create()) {
				$_POST['slide_pic']=sp_asset_relative_url($_POST['slide_pic']);
				$this->slide_model->slide_url=$_POST['slide_url'];
				if ($this->slide_model->save()!==false) {
                    $action="编辑轮播";
                    setAdminLog($action);
                    $this->resetcache();
					$this->success("保存成功！", U("slide/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->slide_model->getError());
			}
				
		}
	}
	
	function delete(){
		if(isset($_POST['ids'])){
			//$ids = implode(",", $_POST['ids']);
			$data['slide_status']=0;
            $where['slide_id']=array('in',$_POST['ids']);
			if ($this->slide_model->where($where)->delete()!==false) {
                $action="删除轮播：{$ids}";
                    setAdminLog($action);
                    $this->resetcache();
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			$id = intval(I("get.id"));
			if ($this->slide_model->delete($id)!==false) {
                $action="删除轮播：{$id}";
                    setAdminLog($action);
                    $this->resetcache();
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		
	}
	
	function toggle(){
		if(isset($_POST['ids']) && $_GET["display"]){
			//$ids = implode(",", $_POST['ids']);
			$data['slide_status']=1;
            $where['slide_id']=array('in',$_POST['ids']);
			if ($this->slide_model->where($where)->save($data)!==false) {
                $this->resetcache();     
				$this->success("显示成功！");
			} else {
				$this->error("显示失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["hide"]){
			//$ids = implode(",", $_POST['ids']);
			$data['slide_status']=0;
            $where['slide_id']=array('in',$_POST['ids']);
			if ($this->slide_model->where($where)->save($data)!==false) {
                $this->resetcache();
				$this->success("隐藏成功！");
			} else {
				$this->error("隐藏失败！");
			}
		}
	}
	    //隐藏
	function ban(){
		
    	$id=intval($_GET['id']);
			$data['slide_status']=0;
    	if ($id) {
    		$rst = $this->slide_model->where(["slide_id" =>$id])->save($data);
    		if ($rst) {
                $action="隐藏轮播：{$id}";
                    setAdminLog($action);
                $this->resetcache();
    			$this->success("幻灯片隐藏成功！");
    		} else {
    			$this->error('幻灯片隐藏失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    //显示
    function cancelban(){
    	$id=intval($_GET['id']);
		$data['slide_status']=1;
    	if ($id) {
    		$result = $this->slide_model->where(["slide_id" =>$id])->save($data);
    		if ($result) {
                $action="显示轮播：{$id}";
                    setAdminLog($action);
                $this->resetcache();     
    			$this->success("幻灯片启用成功！");
    		} else {
    			$this->error('幻灯片启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
	//排序
	public function listorders() {
		$status = parent::_listorders($this->slide_model);
		if ($status) {
            $action="更新轮播排序";
                    setAdminLog($action);
            $this->resetcache();        
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
    
    function resetcache(){
        $key='getSlide';
        $rs=M("slide")->field("slide_pic,slide_url")->where("slide_status='1' and slide_cid='2' ")->order("listorder asc")->select();
        if($rs){
            foreach($rs as $k=>$v){
                $rs[$k]['slide_pic']=get_upload_path($v['slide_pic']);
            }	
            setcaches($key,$rs);
        }
        return 1;
    }
	
}