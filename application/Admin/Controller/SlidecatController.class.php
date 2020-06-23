<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SlidecatController extends AdminbaseController{
	
	protected $slidecat_model;
	
	function _initialize() {
		parent::_initialize();
		$this->slidecat_model = D("Common/SlideCat");
	}
	
	function index(){
		$cats=$this->slidecat_model->where("cat_status!=0")->select();
		$this->assign("slidecats",$cats);
		$this->display();
	}
	
 	/**
     *  添加
     */
    public function add() {
        $this->display();
    }
	
    /**
     *  添加
     */
    public function add_post() {
    	if (IS_POST) {
    		if ($this->slidecat_model->create()) {
    			if ($this->slidecat_model->add()!==false) {
                    $action="添加轮播分类";
                    setAdminLog($action);
    				$this->success("添加成功！", U("slidecat/index"));
    			} else {
    				$this->error("添加失败！");
    			}
    		} else {
    			$this->error($this->slidecat_model->getError());
    		}
    	}
    }
	function edit(){
		$id= intval(I("get.id"));
		$slidecat=$this->slidecat_model->where("cid={$id}")->find();
		$this->assign($slidecat);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			if ($this->slidecat_model->create()) {
				if ($this->slidecat_model->save()!==false) {
                    $action="编辑轮播分类：{$_POST['id']}";
                    setAdminLog($action);
					$this->success("保存成功！", U("slidecat/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->slidecat_model->getError());
			}
		}
	}
	
	
	function delete(){

		$id = intval(I("get.id"));
		if ($this->slidecat_model->delete($id)!==false) {
			$slide_obj=M("Slide");
			$slide_obj->where("slide_cid={$id}")->save(array("slide_cid"=>0));
            $action="删除轮播分类：{$id}";
                    setAdminLog($action);
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
		
	}
	
	
}