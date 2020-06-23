<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class AdController extends AdminbaseController{
	protected $ad_model;
	
	function _initialize() {
		parent::_initialize();
		$this->ad_model = D("Common/Ad");
	}
	
	function index(){
		$ads=$this->ad_model->select();
		$this->assign("ads",$ads);
		$this->display();
	}
	
	function add(){
		$this->display();
	}
	
	function add_post(){
		if(IS_POST){
			if ($this->ad_model->create()){
				if ($this->ad_model->add()!==false) {
					$this->success(L('ADD_SUCCESS'), U("ad/index"));
				} else {
					$this->error(L('ADD_FAILED'));
				}
			} else {
				$this->error($this->ad_model->getError());
			}
		
		}
	}
	
	function edit(){
		$id=(int)I("get.id");
		$ad=$this->ad_model->where(["ad_id"=>$id])->find();
		$this->assign($ad);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			if ($this->ad_model->create()) {
				if ($this->ad_model->save()!==false) {
					$this->success("保存成功！", U("ad/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->ad_model->getError());
			}
		}
	}
	
	/**
	 *  删除
	 */
	function delete(){
		$id = I("get.id",0,"intval");
		if ($this->ad_model->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	function toggle(){
		if(isset($_POST['ids']) && $_GET["display"]){
			//$ids = implode(",", $_POST['ids']);
			$data['status']=1;
            $where['ad_id']=array('in',$_POST['ids']);
			if ($this->ad_model->where($where)->save($data)!==false) {
				$this->success("显示成功！");
			} else {
				$this->error("显示失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["hide"]){
			//$ids = implode(",", $_POST['ids']);
			$data['status']=0;
            $where['ad_id']=array('in',$_POST['ids']);
			if ($this->ad_model->where($where)->save($data)!==false) {
				$this->success("隐藏成功！");
			} else {
				$this->error("隐藏失败！");
			}
		}
	}
	
	
	
	
	
}