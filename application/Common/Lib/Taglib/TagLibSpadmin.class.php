<?php

/**
 */
namespace Common\Lib\TagLib;
use Think\Template\TagLib;
class TagLibSpadmin extends TagLib {

    /**
     * @var type 
     * 标签定义： 
     *                  attr         属性列表 
     *                  close      标签是否为闭合方式 （0闭合 1不闭合），默认为不闭合 
     *                  alias       标签别名 
     *                  level       标签的嵌套层次（只有不闭合的标签才有嵌套层次）
     * 定义了标签属性后，就需要定义每个标签的解析方法了，
     * 每个标签的解析方法在定义的时候需要添加“_”前缀，
     * 可以传入两个参数，属性字符串和内容字符串（针对非闭合标签）。
     * 必须通过return 返回标签的字符串解析输出，在标签解析类中可以调用模板类的实例。
     */
    protected $tags = array(
        //后台模板标签
        'admintpl' => array("attr" => "file", "close" => 0),
    );

    /**
     * 模板包含标签 
     * 格式
     * <admintpl file="APP/模块/模板"/>
     * @staticvar array $_admintemplateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _admintpl($tag, $content) {
        $file = $tag['file'];
        $counts = count($file);
        if ($counts < 3) {
            $file_path = "Admin" .  "/" . $tag['file'];
        } else {
            $file_path = $file[0] . "/" . "Tpl" . "/" . $file[1] . "/" . $file[2];
        }
        //模板路径
        $TemplatePath = sp_add_template_file_suffix( C("SP_ADMIN_TMPL_PATH") .C("SP_ADMIN_DEFAULT_THEME")."/". $file_path );
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            return false;
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板内容
        $parseStr = $this->tpl->parse($tmplContent);
        return $parseStr;
    }
    
    

}

