<?php 
// +---------------------------------------------------------------------- 
// | YBlog 
// +---------------------------------------------------------------------- 
// | Copyright (c) 2008 http://www.yhustc.com All rights reserved. 
// +---------------------------------------------------------------------- 
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 ) 
// +---------------------------------------------------------------------- 
// | Author: yhustc <yhustc@gmail.com> 
// +---------------------------------------------------------------------- 
// $Id$ 
/** 
 +------------------------------------------------------------------------------ 
 * RSS生成类 
 +------------------------------------------------------------------------------ 
 * @author    yhustc <yhustc@gmail.com> 
 * @version   $Id$ 
 +------------------------------------------------------------------------------ 
 */
namespace Common\Lib\RSS;
class RSS{ 
    /** 
     +---------------------------------------------------------- 
     * RSS频道名 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $channel_title = ''; 
    /** 
     +---------------------------------------------------------- 
     * RSS频道链接 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $channel_link = ''; 
    /** 
     +---------------------------------------------------------- 
     * RSS频道描述 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $channel_description = ''; 
    /** 
     +---------------------------------------------------------- 
     * RSS频道使用的小图标的URL 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $channel_imgurl = ''; 
    /** 
     +---------------------------------------------------------- 
     * RSS频道所使用的语言 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $language = 'zh_CN'; 
    /** 
     +---------------------------------------------------------- 
     * RSS文档创建日期，默认为今天 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $pubDate = ''; 
    protected $lastBuildDate = ''; 
    protected $generator = 'YBlog RSS Generator'; 
    /** 
     +---------------------------------------------------------- 
     * RSS单条信息的数组 
     +---------------------------------------------------------- 
     * @var string 
     * @access protected 
     +---------------------------------------------------------- 
     */
    protected $items = array(); 
    /** 
     +---------------------------------------------------------- 
     * 构造函数 
     +---------------------------------------------------------- 
     * @access public  
     +---------------------------------------------------------- 
     * @param string $title  RSS频道名 
     * @param string $link  RSS频道链接 
     * @param string $description  RSS频道描述 
     * @param string $imgurl  RSS频道图标 
     +---------------------------------------------------------- 
     */
    public function __construct($title, $link, $description, $imgurl = '') 
    { 
        $this->channel_title = $title; 
        $this->channel_link = $link; 
        $this->channel_description = $description; 
        $this->channel_imgurl = $imgurl; 
        $this->pubDate = Date('Y-m-d H:i:s', time()); 
        $this->lastBuildDate = Date('Y-m-d H:i:s', time()); 
    } 

    /** 
     +---------------------------------------------------------- 
     * 设置私有变量 
     +---------------------------------------------------------- 
     * @access public  
     +---------------------------------------------------------- 
     * @param string $key  变量名 
     * @param string $value  变量的值 
     +---------------------------------------------------------- 
     */
     public function Config($key,$value) 
     { 
        $this->{$key} = $value; 
     } 
    /** 
     +---------------------------------------------------------- 
     * 添加RSS项 
     +---------------------------------------------------------- 
     * @access public  
     +---------------------------------------------------------- 
     * @param string $title  日志的标题 
     * @param string $link  日志的链接 
     * @param string $description  日志的摘要 
     * @param string $pubDate  日志的发布日期 
     +---------------------------------------------------------- 
     */
     function AddItem($title, $link, $description, $pubDate) 
     { 
        $this->items[] = array('title' => $title, 'link' => $link, 'description' => $description, 'pubDate' => $pubDate); 
     } 
     /** 
     +---------------------------------------------------------- 
     * 输出RSS的XML为字符串 
     +---------------------------------------------------------- 
     * @access public  
     +---------------------------------------------------------- 
     * @return string 
     +---------------------------------------------------------- 
     */
    public function Fetch() 
    { 
        $rss = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n"; 
        $rss = "<rss version=\"2.0\">\r\n"; 
        $rss .= "<channel>\r\n"; 
        $rss .= "<title><![CDATA[{$this->channel_title}]]></title>\r\n"; 
        $rss .= "<description><![CDATA[{$this->channel_description}]]></description>\r\n"; 
        $rss .= "<link>{$this->channel_link}</link>\r\n"; 
        $rss .= "<language>{$this->language}</language>\r\n"; 
        if (!empty($this->pubDate)) 
            $rss .= "<pubDate>{$this->pubDate}</pubDate>\r\n"; 
        if (!empty($this->lastBuildDate)) 
            $rss .= "<lastBuildDate>{$this->lastBuildDate}</lastBuildDate>\r\n"; 
        if (!empty($this->generator)) 
            $rss .= "<generator>{$this->generator}</generator>\r\n"; 
        $rss .= "<ttl>5</ttl>\r\n"; 
        if (!empty($this->channel_imgurl)) { 
            $rss .= "<image>\r\n"; 
            $rss .= "<title><![CDATA[{$this->channel_title}]]></title>\r\n"; 
            $rss .= "<link>{$this->channel_link}</link>\r\n"; 
            $rss .= "<url>{$this->channel_imgurl}</url>\r\n"; 
            $rss .= "</image>\r\n"; 
        } 

        for ($i = 0; $i < count($this->items); $i++) { 
            $rss .= "<item>\r\n"; 
            $rss .= "<title><![CDATA[{$this->items[$i]['title']}]]></title>\r\n"; 
            $rss .= "<link>{$this->items[$i]['link']}</link>\r\n"; 
            $rss .= "<description><![CDATA[{$this->items[$i]['description']}]]></description>\r\n"; 
            $rss .= "<pubDate>{$this->items[$i]['pubDate']}</pubDate>\r\n"; 
            $rss .= "</item>\r\n"; 
        } 
        $rss .= "</channel>\r\n</rss>"; 
        return $rss; 
    } 

    /** 
     +---------------------------------------------------------- 
     * 输出RSS的XML到浏览器 
     +---------------------------------------------------------- 
     * @access public  
     +---------------------------------------------------------- 
     * @return void 
     +---------------------------------------------------------- 
     */

    public function Display() 
    { 
        header("Content-Type: text/xml; charset=utf-8"); 
        echo $this->Fetch(); 
        exit; 
    } 
} 

