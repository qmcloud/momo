<?php
// +---------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +---------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +---------------------------------------------------------------------

namespace Api\Controller;
use Common\Controller\AppframeController;

/**
 * 插件控制器
 * 用于调度各个插件的URL访问需求
 */
class PluginController extends AppframeController{
    
    //5iymt 增加插件跳转页设置 2015-6-12 目录为插件视图目录下的success.html,error.html文件
	public function __construct(){
		C("TMPL_ACTION_SUCCESS",'/success');
		C("TMPL_ACTION_ERROR",'/error');
		parent::__construct();
	}
	
	public $config=array();
	
	public $name='s';
	
	public $action_name;
	
	public $controller_name;

	public function execute($_plugin = null, $_controller = null, $_action = null){
		if(C('URL_CASE_INSENSITIVE')){
			$_plugin = ucfirst(parse_name($_plugin, 1));
			$_controller = parse_name($_controller,1);
		}

		if(!empty($_plugin) && !empty($_controller) && !empty($_action)){
			
			$class   =  "plugins\\{$_plugin}\\{$_plugin}Plugin";
			$plugin   = new $class();
			
			$plugin_controller = A("plugins://{$_plugin}/{$_controller}");
			
			$plugin_controller->action_name=$_action;
			$plugin_controller->controller_name=$_controller;
			
			define("PLUGIN_CONTROLLER_NAME", ucfirst($_controller));
			define("PLUGIN_ACTION_NAME", $_action);
			
			$plugin_controller->name=$plugin->getName();
			$plugin_controller->config=$plugin->getConfig();
			$plugin_controller->$_action();
		} else {
			$this->error('没有指定插件名称，控制器或操作！');
		}
	}
	
	public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		parent::display($this->parseTemplate($templateFile), $charset, $contentType);
	}
	
	public function fetch($templateFile='',$content='',$prefix=''){
		return parent::fetch($this->parseTemplate($templateFile),$content,$prefix);
	}
	
	/**
	 * 自动定位模板文件
	 * @access protected
	 * @param string $template 模板文件规则
	 * @return string
	 */
	public function parseTemplate($template='') {
		$plugin=$this->name;
		
		$plugin_config=$this->config;
		
		$theme=$plugin_config['theme'];
		
		$depr = "/";
		
		if(empty($theme)){
			$theme="";
		}else{
			$theme=$depr.$theme;
		}
		
		
		$template   =   str_replace(':', $depr, $template);
		
		// 分析模板文件规则
		if('' == $template) {
			// 如果模板文件名为空 按照默认规则定位
			$template = "/".PLUGIN_CONTROLLER_NAME . $depr . PLUGIN_ACTION_NAME;
		}elseif(false === strpos($template, '/')){
			$template = "/".PLUGIN_CONTROLLER_NAME . $depr . $template;
		}
		
		$v_layer=C("DEFAULT_V_LAYER");
		
		$file = sp_add_template_file_suffix("./plugins/$plugin/$v_layer".$theme.$template);
		if(!file_exists_case($file)) E(L('_TEMPLATE_NOT_EXIST_').':'.$file);
		return $file;

	}

}