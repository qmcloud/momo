<?php
namespace Common\Model;
use Common\Model\CommonModel;
class CommentsModel extends CommonModel{
	
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('full_name', 'check_full_name', '姓名不能为空！', 1, 'callback', CommonModel:: MODEL_INSERT  ),
			//array('email', 'check_full_email', '邮箱不能为空！', 1, 'callback', CommonModel:: MODEL_INSERT ),
			array('content', 'require', '评论不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			//array('email','email','邮箱格式不正确！',0,'',CommonModel:: MODEL_BOTH ),
			array('post_table','table_exists','您评论的内容不存在！',0,'callback',CommonModel:: MODEL_BOTH ),
	);
	
	protected $_auto = array(
			array('createtime','mDate',1,'callback'), // 对msg字段在新增的时候回调htmlspecialchars方法
			
	);
	
	function mDate(){
		return date("Y-m-d H:i:s");
	}
	
	function check_full_name($data){
		if(empty($data)){
			if(isset($_SESSION["user"])){
				return true;
			}
			return false;
		}
		
		return true;
	}
	
	function check_full_email($data){
		if(empty($data)){
			if(isset($_SESSION["user"])){
				return true;
			}
			return false;
		}
		
		return true;
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	
	
	protected function _after_insert($data,$options){
		parent::_after_insert($data,$options);
		$id=$data['id'];
		$parent_id=$data['parentid'];
		if($parent_id==0){
			$d['path']="0-$id";
		}else{
			$parent=$this->where("id=$parent_id")->find();
			$d['path']=$parent['path'].'-'.$id;
		}
		$this->where("id=$id")->save($d);
	}
	
	
	protected function _after_update($data,$options){
		parent::_after_update($data,$options);
		
		if(isset($data['parentid'])){
			$id=$data['id'];
			$parent_id=$data['parentid'];
			if($parent_id==0){
				$d['path']="0-$id";
			}else{
				$parent=$this->where("id=$parent_id")->find();
				$d['path']=$parent['path'].'-'.$id;
			}
			
			$this->where("id=$id")->save($d);
		}
		
	}
}