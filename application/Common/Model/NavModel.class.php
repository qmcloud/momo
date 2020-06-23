<?php
namespace Common\Model;
use Common\Model\CommonModel;
class NavModel extends CommonModel {
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('label', 'require', '菜单标签不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			
	);
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	
}