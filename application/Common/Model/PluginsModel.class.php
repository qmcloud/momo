<?php
namespace Common\Model;
use Common\Model\CommonModel;
class PluginsModel extends CommonModel{
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			//array('ad_name', 'require', '广告名称不能为空！', 1, 'regex', 3),
	);
	
	/**
	 * 获取插件列表
	 */
	public function getList(){
		$dirs = array_map('basename',glob('./plugins/*', GLOB_ONLYDIR));
		if($dirs === false){
			$this->error = '插件目录不可读';
			return false;
		}
		$plugins			=	array();
		$where['name']	=	array('in',$dirs);
		$list			=	$this->where($where)->field(true)->select();
		foreach($list as $plugin){
			$plugins[$plugin['name']]	=	$plugin;
		}
		foreach ($dirs as $value) {
			if(!isset($plugins[$value])){
				$class = sp_get_plugin_class($value);
				if(!class_exists($class)){ // 实例化插件失败忽略
					\Think\Log::record('插件'.$value.'的入口文件不存在！');
					continue;
				}
				$obj    =   new $class;
				$plugins[$value]	= $obj->info;
				
				if(!isset($obj->info['type']) || $obj->info['type']==1){//只获取普通插件，微信插件在微信中使用
					if($plugins[$value]){
						$plugins[$value]['status']=3;//未安装
					}
				}else{
					unset($plugins[$value]);
				}
				
			}
		}
		return $plugins;
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
}