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

namespace Common\Lib;

/**
 * 插件类
 */
abstract class Plugin{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;

    /**
     * $info = array(
     *  'name'=>'Helloworld',
     *  'title'=>'Helloworld',
     *  'description'=>'Helloworld',
     *  'status'=>1,
     *  'author'=>'thinkcmf',
     *  'version'=>'1.0'
     *  )
     */
    public $info                =   array();
    public $plugin_path          =   '';
    public $config_file         =   '';
    public $custom_config       =   '';
    public $admin_list          =   array();
    public $custom_adminlist    =   '';
    public $access_url          =   array();
    public $tmpl_root ="";

    public function __construct(){
        $this->view         =   \Think\Think::instance('Think\View');
        $this->plugin_path   =   './plugins/'.$this->getName().'/';
        
        //多语言
        if (C('LANG_SWITCH_ON',null,false)){
            $lang_file= $this->plugin_path."Lang/".LANG_SET.".php";
            if(is_file($lang_file)){
                $lang=include $lang_file;
                L($lang);
            }
        }
        $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        
        $plugin_root= __ROOT__ . '/plugins/'.$this->getName();
        
        $TMPL_PARSE_STRING['__PLUGINROOT__'] =$plugin_root;
        
        if(is_file($this->plugin_path.'config.php')){
        	$this->config_file = $this->plugin_path.'config.php';
        }
        
        $config=$this->getConfig();
        $theme=$config['theme'];
         
        $depr = "/";
         
        $theme=empty($theme)?"":$theme.$depr;
        
        $v_layer=C("DEFAULT_V_LAYER");
        
        $this->tmpl_root= "plugins/".$this->getName()."/$v_layer/".$theme;
        
        $TMPL_PARSE_STRING['__PLUGINTMPL__'] = __ROOT__."/".$this->tmpl_root;
        
        C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
        
    }

    /**
     * 模板主题设置
     * @access protected
     * @param string $theme 模版主题
     * @return Action
     */
    final protected function theme($theme){
        $this->view->theme($theme);
        return $this;
    }

    //显示方法
    final protected function display($template='widget'){
        echo ($this->fetch($template));
    }
    

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     */
    final protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }


    //用于显示模板的方法
    final protected function fetch($templateFile = 'widget'){
        if(!is_file($templateFile)){
        	
        	$config=$this->getConfig();
        	$theme=$config['theme'];
        	
        	$depr = "/";
        	
        	$theme=empty($theme)?"":$theme.$depr;
        	
            $templateFile = sp_add_template_file_suffix("./".$this->tmpl_root.$templateFile);
            if(!file_exists_case($templateFile)){
                throw new \Exception("模板不存在:$templateFile");
            }
        }
        return $this->view->fetch($templateFile);
    }

    final public function getName(){
        $class = get_class($this);
        return substr($class,strrpos($class, '\\')+1, -6);
    }

    final public function checkInfo(){
        $info_check_keys = array('name','title','description','status','author','version');
        foreach ($info_check_keys as $value) {
            if(!array_key_exists($value, $this->info))
                return false;
        }
        return true;
    }

    /**
     * 获取插件的配置数组
     */
    public function getConfig($name=''){
    	
        static $_config = array();
        if(empty($name)){
            $name = $this->getName();
        }
        if(isset($_config[$name])){
        	return $_config[$name];
        }
        
        $config=M('Plugins')->where(array("name"=>$name))->getField("config");
        if($config){
            $config   =   json_decode($config, true);
        }else{
        	
            $temp_arr = include $this->config_file;
            foreach ($temp_arr as $key => $value) {
                if($value['type'] == 'group'){
                    foreach ($value['options'] as $gkey => $gvalue) {
                        foreach ($gvalue['options'] as $ikey => $ivalue) {
                            $config[$ikey] = $ivalue['value'];
                        }
                    }
                }else{
                    $config[$key] = $temp_arr[$key]['value'];
                }
            }
        }
        $_config[$name]     =   $config;
        return $config;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
