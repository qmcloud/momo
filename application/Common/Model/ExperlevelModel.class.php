<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ExperlevelModel extends  CommonModel{
	
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('levelid', 'number', '等级必须为数字！', 1, 'regex', 3),
			array('levelname', 'require', '名称不能为空！', 1, 'regex', 3),
			array('level_up', 'number', '等级上限必须为数字！', 1, 'regex', 3),
	);
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	
}