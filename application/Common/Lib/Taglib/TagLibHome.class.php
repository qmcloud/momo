<?php
namespace Common\Lib\TagLib;
use Think\Template\TagLib;
class TagLibHome extends TagLib {

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
        'tc_include' => array("attr" => "file", "close" => 0),
    );

    /**
     * 模板包含标签 
     * 格式
     * <tc_include file=""/>
     * @staticvar array $_tc_include_templateParseCache
     * @param type $tag 属性数据
     * @param type $content 标签内容
     * @return array 
     */
    public function _tc_include($tag, $content) {
        static $_tc_include_templateParseCache = array();
        $file = str_replace(":", "/", $tag['file']);
        $cacheIterateId = md5($file . $content);
        if (isset($_tc_include_templateParseCache[$cacheIterateId])) {
           return $_tc_include_templateParseCache[$cacheIterateId];
        }
        //模板路径
        $TemplatePath = sp_add_template_file_suffix( C("SP_TMPL_PATH") .C('SP_DEFAULT_THEME')."/". $file ) ;
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            return false;
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板内容
        $parseStr = $this->tpl->parse($tmplContent);
        $_tc_include_templateParseCache[$cacheIterateId] = $parseStr;
        return $_tc_include_templateParseCache[$cacheIterateId];
    }
    
    

}

