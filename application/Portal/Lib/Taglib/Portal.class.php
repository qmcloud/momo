<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Lib\TagLib;
use Think\Template\TagLib;
class Portal extends TagLib {

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
    	'articles'   =>  array('attr'=>'cid,field,limit,pagesize,pagename,where,order','level'=>3),
    );

    /**
     * articles标签解析 循环输出数据集
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
    public function _articles($tag,$content) {
    	$field      =   !empty($tag['field'])?$tag['field']:'*';
    	$limit      =   !empty($tag['limit'])?$tag['limit']:'10';
    	$order      =   !empty($tag['order'])?$tag['order']:'post_modified desc';
    	$pagesize   =   !empty($tag['pagesize'])?$tag['pagesize']:'0';
    	$pagetpl    =   !empty($tag['pagetpl'])?$tag['pagetpl']:'{first}{prev}{liststart}{list}{listend}{next}{last}';
    	$item       =   !empty($tag['item'])?$tag['item']:'vo';
    	$key        =   !empty($tag['key'])?$tag['key']:'key';
    	//$where      =   $tag['where'];
    	
    	//$where      =   $this->autoBuildVar($where);
    	
    	
    	//根据参数生成查询条件
    	$where['status'] = array('eq',1);
    	$where['post_status'] = array('eq',1);
    	
    	if (isset($tag['cid'])) {
    		$where['term_id'] = array('in',$tag['cid']);
    	}
    	
    	
    	$where=var_export($where, true);
    	
    	$parseStr   =   "<?php \$posts=sp_posts('field:$field;limit:$limit;order:$order;',$where,$pagesize,'','$pagetpl');\$articles=\$posts['posts']?>\n";
    	$parseStr  .=   '<?php if(is_array($articles)): foreach($articles as $'.$key.'=>$'.$item.'): ?>';
    	$parseStr  .=   $this->tpl->parse($content);
    	$parseStr  .=   '<?php endforeach; endif; ?>';
    
    	if(!empty($parseStr)) {
    		return $parseStr;
    	}
    	return ;
    }
    
    
    

}

