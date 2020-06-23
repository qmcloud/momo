<?php

/*
 * 模板分页类,源于Easp的数据库分页方法，算是Easp分页的的PHP独立版
 * 支持动态和静态分页方式
 * Easp官网http://easp.lengshi.com/
 * 作者：钟晶晶
 * 日期：2010-11-3
 * 邮箱：zhongjingjing@gmail.com
 * 博客：http://blog.zaimer.com
 * Page([总记录数=1]，[分页大小=20]，[当前页=1]，[显示页数=6]，[分页参数='page'],[分页链接=当前页面],[是否静态=FALSE])
 * 动态：
 * 简单用法：
 * $page = new Page(50);
 * $page->SetPager('zjj','<div class="newpager">共有{recordcount} 个商品&nbsp;&nbsp;当前第&nbsp;{pageindex}&nbsp;页&nbsp;/&nbsp;共&nbsp;{pagecount}&nbsp;页&nbsp;分页：&nbsp;{first}{prev}&nbsp;&nbsp;{list}&nbsp;&nbsp;{next}{last}&nbsp;&nbsp;转到&nbsp;{jump}&nbsp;页</div>',array("listlong"=>"6","first"=>"首页","last"=>"尾页","prev"=>"上一页","next"=>"下一页","list"=>"第*页","jump"=>"select"));
 * echo $page->show('zjj');
 * 全参数用法：
 * $page = new Page(50,20,1,6,'page','prrr.php',false);
 * $page->SetPager('zjj','<div class="newpager">共有{recordcount} 个商品&nbsp;&nbsp;当前第&nbsp;{pageindex}&nbsp;页&nbsp;/&nbsp;共&nbsp;{pagecount}&nbsp;页&nbsp;分页：&nbsp;{first}{prev}&nbsp;&nbsp;{list}&nbsp;&nbsp;{next}{last}&nbsp;&nbsp;转到&nbsp;{jump}&nbsp;页</div>',array("listlong"=>"6","first"=>"首页","last"=>"尾页","prev"=>"上一页","next"=>"下一页","list"=>"第*页","jump"=>"select"));
 * echo $page->show('zjj');
 * 静态：
 * $page = new Page(50,20,1,6,'page','prrr{page}.html',true);
 * $page->SetPager('zjj','<div class="newpager">共有{recordcount} 个商品&nbsp;&nbsp;当前第&nbsp;{pageindex}&nbsp;页&nbsp;/&nbsp;共&nbsp;{pagecount}&nbsp;页&nbsp;分页：&nbsp;{first}{prev}&nbsp;&nbsp;{list}&nbsp;&nbsp;{next}{last}&nbsp;&nbsp;转到&nbsp;{jump}&nbsp;页</div>',array("listlong"=>"6","first"=>"首页","last"=>"尾页","prev"=>"上一页","next"=>"下一页","list"=>"第*页","jump"=>"select"));
 * echo $page->show('zjj');
  其他说明：
  $page = new Page($Total_Size , $Page_Size $Current_Page , $List_Page, $PageParam , $PageLink ,$Static);
  SetPager()方法设置分页导航列表样式语法
  $page->SetPager($name, $html, $config);
  参数说明：
  $name:
  String (字符串)  分页导航样式配置名称，默认样式的名称为”default”

  $html:
  String (字符串)
  分页导航样式HTML模板，可以用以下代码嵌入HTML代码中代表相应的项目(均为可选)：
  “{recordcount}” - 总记录数
  “{pagecount}” - 总页数
  “{pageindex}” - 当前页码
  “{pagesize}” - 每页记录数
  “{list}” - 分页链接列表
  “{liststart}” - 分页链接列表页首导航
  “{listend}” - 分页链接列表页尾导航
  “{first}” - 第一页的链接
  “{last}” - 最后页的链接
  “{prev}” - 上一页的链接
  “{next}” - 下一页的链接
  “{jump}” - 页面跳转文本框或下拉菜单

  $config:
  “” (空字符串) 或 Array (数组)
  分页导航样式配置选项。如果留空将采用默认配置，否则使用数组配置，可配置项目包括：
  “listlong” - 分页链接列表页码数量，默认为9
  “listsidelong” - 分页链接列表首尾导航页码数量，默认为2，html 参数中有”{liststart}”或”{listend}”时才有效
  “list” - 分页链接的HTML代码，用*代表页码，默认为仅显示页码
  “currentclass” - 当前页码的CSS样式名称，默认为”current”
  “link” - 自定义页码链接，用*代表页码，用于静态页面分页或Ajax分页
  “first” - 第一页链接的HTML代码，默认为 ”«”，即显示为 «
  “prev” - 上一页链接的HTML代码，默认为”‹”,即显示为 ‹
  “next” - 下一页链接的HTML代码，默认为”›”,即显示为 ›
  “last” - 最后页链接的HTML代码，默认为”»”,即显示为 »
  “more” - 被省略的页码链接显示为，默认为”…”
  “disabledclass” - 当处于首尾页时不可用链接的CSS样式名称，默认为”disabled”
  “jump” - 页面跳转方式，默认为”input”文本框，可设置为”select”下拉菜单
  “jumpplus” - 页面跳转文本框或下拉菜单的附加内部代码
  “jumpaction” - 跳转时要执行的javascript代码，用*代表页码，可用于Ajax分页
  “jumplong” - 当跳转方式为下拉菜单时最多同时显示的页码数量，0为全部显示，默认为50
 */

class Page {

    private $Page_size; //每页显示的条目数
    private $Total_Size; //总条目数
    private $Current_page; //当前被选中的页
    private $List_Page; //每次显示的页数  默认列表每页显示行数
    private $Total_Pages = 20; //总页数
    private $Page_tpl = array(); // 分页模板
    private $PageParam;
    private $PageLink;
    private $Static;
    // 起始行数
    public  $firstRow;
    public  $listRows;
    private $linkwraper="";
    private $linkwraper_pre="";
    private $linkwraper_after="";
    private $searching=false;

    //Page([总记录数=1]，   [分页大小=20]，     [当前页=1]，         [显示页数=6]，     [分页参数='page'],      [分页链接=当前页面],[是否静态=FALSE])
    function __construct($Total_Size = 1, $Page_Size = 20, $Current_Page = 1, $List_Page = 6, $PageParam = 'p', $PageLink = '', $Static = FALSE) {
        $this->Page_size = intval($Page_Size);
        $this->Total_Size = intval($Total_Size);
        if (!$Current_Page) {
            $this->Current_page = 1;
        } else {
            $this->Current_page = (int)$Current_Page<1?1:(int)$Current_Page;
        }
        //总分页数
        $this->Total_Pages = ceil($Total_Size / $Page_Size);
        //一次显示多少个链接 ，该参数会被SetPager里传入的配置覆盖。
        $this->List_Page = (int)$List_Page;
        //接收分页参数的标识符
        $this->PageParam = $PageParam;
        //当前页面地址，当需要生成静态地址，此参数需要给，分页号用{page}
        $this->PageLink = (empty($PageLink) ? $_SERVER ["PHP_SELF"] : $PageLink);
        //是否开启静态
        $this->Static = $Static;

        $this->Page_tpl ['default'] = array('Tpl' => '<div class="pager">{first}{prev}{liststart}{list}{listend}{next}{last} 跳转到{jump}页</div>', 'Config' => array());

        $this->GetCurrentPage();

        $this->listRows = $Page_Size;

        $this->firstRow = ($this->Current_page - 1) * $this->listRows;
        if($this->firstRow < 0){
            $this->firstRow = 0;
        }
    }

    public function __set($Param, $value) {
        $this->$Param = $value;
    }

    public function __get($Param) {
        return $this->$Param;
    }
    public function setLinkWraper($wraper){
    	if(empty($wraper)){
    		
    	}else{
    	    $this->linkwraper=$wraper;
    		$this->linkwraper_after="</$wraper>";
    		$this->linkwraper_pre="<$wraper>";
    	}
    }

    private function UrlParameters($url = array()) {
    	unset($url[C('VAR_MODULE')]);
    	unset($url[C('VAR_CONTROLLER')]);
    	unset($url[C('VAR_ACTION')]);
        foreach ($url as $key => $val) {
            if ($key != $this->PageParam && $key != "_URL_")
                $arg [$key] = $val;
        }
        $arg[$this->PageParam] = '*';
        if ($this->Static) {
            //当启用静态地址，$this->PageLink传入的是array，并且包含两个 index,list
            /*
             * array(
             *    "index"=>"http://www.a.com/192.html",//这种是表示当前是首页，无需加分页1
             *    "list"=>"http://www.a.com/192-{page}.html",//这种表示分页非首页时启用
             * )
             */
            if (is_array($this->PageLink)) {
                return str_replace('{page}', '*', $this->PageLink['list']);
            } else {
                return str_replace('{page}', '*', $this->PageLink);
            }
        } else {
        	
        	if($this->searching){
        		$url=leuu(MODULE_NAME  . "/" . CONTROLLER_NAME . "/" . ACTION_NAME)."?".http_build_query ($arg);
        	}else{
        		$url=leuu(MODULE_NAME  . "/" . CONTROLLER_NAME . "/" . ACTION_NAME,$arg);
        	}
        	return str_replace("%2A", "*", $url);
        	
        }
    }

    public function SetPager($Tpl_Name = 'default', $Tpl = '', $Config = array()) {
        if (empty($Tpl))
            $Tpl = $this->Page_tpl ['default'] ['Tpl'];
        if (empty($Config))
            $Config = $this->Page_tpl ['default'] ['Config'];
        $this->Page_tpl [$Tpl_Name] = array('Tpl' => $Tpl, 'Config' => $Config);
    }

    public function show($Tpl_Name = 'default') {
        //当分页数只有1的时候，不显示
        if ($this->Total_Pages <= 1) {
            return;
        }
        return $this->Pager($this->Page_tpl [$Tpl_Name]);
    }

    public function GetCurrentPage() {
    	$p=isset($_GET [$this->PageParam])?intval($_GET [$this->PageParam]):1;
    	$p=$p < 1 ? 1 : $p;
    	$total_pages=intval($this->Total_Pages);
        $this->Current_page = ($p <=$total_pages? $p : $total_pages);
    }

    public function Pager($Page_tpl = '') {
        if (empty($Page_tpl))
            $Page_tpl = $this->Page_tpl ['default'];
        $cfg = array('recordcount' => intval($this->Total_Size), 'pageindex' => intval($this->Current_page), 'pagecount' => intval($this->Total_Pages), 'pagesize' => intval($this->Page_size), 'listlong' => intval($this->List_Page), 'listsidelong' => 2, 'list' => '*', 'currentclass' => 'current', 'link' => $this->UrlParameters($_GET), 'first' => '&laquo;', 'prev' => '&#8249;', 'next' => '&#8250;', 'last' => '&raquo;', 'more' => $this->linkwraper_pre.'<span>...</span>'.$this->linkwraper_after, 'disabledclass' => 'disabled', 'jump' => 'input', 'jumpplus' => '', 'jumpaction' => '', 'jumplong' => 50);
        if (!empty($Page_tpl ['Config'])) {
            foreach ($Page_tpl ['Config'] as $key => $val) {
                if (array_key_exists($key, $cfg))
                    $cfg [$key] = $val;
            }
        }
		//判断listlong是否为偶数
		if( (int)$cfg ['listlong']%2 != 0  ){
		   $cfg ['listlong'] = $cfg ['listlong']+1;
		}
		
        $tmpStr = $Page_tpl ['Tpl'];
        $pStart = $cfg ['pageindex'] - (($cfg ['listlong'] / 2) + ($cfg ['listlong'] % 2)) + 1;
        $pEnd = $cfg ['pageindex'] + $cfg ['listlong'] / 2;
        if ($pStart < 1) {
            $pStart = 1;
            $pEnd = $cfg ['listlong'];
        }
        if ($pEnd > $cfg ['pagecount']) {
            $pStart = $cfg ['pagecount'] - $cfg ['listlong'] + 1;
            $pEnd = $cfg ['pagecount'];
        }
		
        if ($pStart < 1)
            $pStart = 1;
        for ($i = $pStart; $i <= $pEnd; $i++) {
            if ($i == $cfg ['pageindex']) {
                $wraper= empty($this->linkwraper)?'':'<'.$this->linkwraper.' class="active '.$cfg ['currentclass'].'">';
                $pList .=$wraper.'<span class="' . $cfg ['currentclass'] . '" >' . str_replace('*', $i, $cfg ['list']) . '</span> '.$this->linkwraper_after;
            } else {
                //此处是为了照顾静态地址生成时，第一页不显示当前分页1，启用该方法，静态地址需要$this->PageLink传入的是array，并且包含两个 index,list。index是首页
                //事例用法  new Page(50,5,2,6,"page",array("index"=>"http://www.a.com/192.html","list"=>"http://www.a.com/192-{page}.html",),true);
                if ($this->Static && $i == 1) {
                    $pList .= $this->linkwraper_pre.'<a href="' . $this->PageLink['index'] . '"> ' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                } else {
                    $pList .= $this->linkwraper_pre.'<a href="' . str_replace('*', $i, $cfg ['link']) . '"> ' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                }
            }
        }
        if ($cfg ['listsidelong'] > 0) {
            if ($cfg ['listsidelong'] < $pStart) {
                for ($i = 1; $i <= $cfg ['listsidelong']; $i++) {
					if ($this->Static && $i == 1) {
						$pListStart .= $this->linkwraper_pre.'<a href="' . $this->PageLink['index'] . '">' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                     } else {
                        $pListStart .= $this->linkwraper_pre.'<a href="' . str_replace('*', $i, $cfg ['link']) . '">' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                    }
                }
                $pListStart .= ($cfg ['listsidelong'] + 1) == $pStart ? '' : $cfg ['more'] . ' ';
            } else {
                if ($cfg ['listsidelong'] >= $pStart && $pStart > 1) {
                    for ($i = 1; $i <= ($pStart - 1); $i++) {
                        if ($this->Static && $i == 1) {
                            $pListStart .= $this->linkwraper_pre.'<a href="' . $this->PageLink['index'] . '"> ' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                        } else {
                            $pListStart .= $this->linkwraper_pre.'<a href="' . str_replace('*', $i, $cfg ['link']) . '"> ' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                        }
                    }
                }
            }
            if (($cfg ['pagecount'] - $cfg ['listsidelong']) > $pEnd) {
                $pListEnd = ' ' . $cfg ['more'] . $pListEnd;
                for ($i = (($cfg ['pagecount'] - $cfg ['listsidelong']) + 1); $i <= $cfg ['pagecount']; $i++) {
					if ($this->Static && $i == 1) {
						$pListEnd .= $this->linkwraper_pre.'<a href="' . $this->PageLink['index'] . '">' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                     } else {
                        $pListEnd .= $this->linkwraper_pre.'<a href="' . str_replace('*', $i, $cfg ['link']) . '"> ' . str_replace('*', $i, $cfg ['list']) . ' </a> '.$this->linkwraper_after;
                    }
                }
            } else {
                if (($cfg ['pagecount'] - $cfg ['listsidelong']) <= $pEnd && $pEnd < $cfg ['pagecount']) {
                    for ($i = ($pEnd + 1); $i <= $cfg ['pagecount']; $i++) {
						if ($this->Static && $i == 1) {
						    $pListEnd .= '<a href="' . $this->PageLink['index'] . '">' . str_replace('*', $i, $cfg ['list']) . '</a> '.$this->linkwraper_after;
                        } else {
                            $pListEnd .= $this->linkwraper_pre.'<a href="' . str_replace('*', $i, $cfg ['link']) . '"> ' . str_replace('*', $i, $cfg ['list']) . ' </a> '.$this->linkwraper_after;
                        }
                    }
                }
            }
        }
        //上一页 首页
        if ($cfg ['pageindex'] > 1) {
            if ($this->Static) {
                $pFirst = $this->linkwraper_pre.'<a href="' . $this->PageLink['index'] . '">' . $cfg ['first'] . '</a> '.$this->linkwraper_after; //首页
            } else {
                $pFirst = $this->linkwraper_pre.'<a href="' . str_replace('*', 1, $cfg ['link']) . '">' . $cfg ['first'] . '</a> '.$this->linkwraper_after; //首页
            }
            if ($this->Static && ($cfg ['pageindex'] - 1) == 1) {
                $pPrev = $this->linkwraper_pre.'<a href="' . $this->PageLink['index'] . '">' . $cfg ['prev'] . '</a> '.$this->linkwraper_after; //上一页
            } else {
                $pPrev = $this->linkwraper_pre.'<a href="' . str_replace('*', $cfg ['pageindex'] - 1, $cfg ['link']) . '">' . $cfg ['prev'] . '</a> '.$this->linkwraper_after;
            }
        }
        //下一页，尾页
        if ($cfg ['pageindex'] < $cfg ['pagecount']) {
            $pLast = $this->linkwraper_pre.'<a href="' . str_replace('*', $cfg ['pagecount'], $cfg ['link']) . '">' . $cfg ['last'] . '</a> '.$this->linkwraper_after;
            $pNext = $this->linkwraper_pre.'<a href="' . str_replace('*', $cfg ['pageindex'] + 1, $cfg ['link']) . '">' . $cfg ['next'] . '</a> '.$this->linkwraper_after;
        }

        //快捷跳转方式
        switch (strtolower($cfg ['jump'])) {
            case 'input' :
                $pJumpValue = 'this.value';
                $pJump = '<input type="text" size="3" title="请输入要跳转到的页数并回车"' . (($cfg ['jumpplus'] == '') ? '' : ' ' . $cfg ['jumpplus']);
                $pJump .= ' onkeydown="javascript:if(event.charCode==13||event.keyCode==13){if(!isNaN(' . $pJumpValue . ')){';
                $pJump .= ($cfg ['jumpaction'] == '' ? ((strtolower(substr($cfg ['link'], 0, 11)) == 'javascript:') ? str_replace('*', $pJumpValue, substr($cfg ['link'], 12)) : " document.location.href='" . str_replace('*', '\'+' . $pJumpValue . '+\'', $cfg ['link']) . '\';') : str_replace("*", $pJumpValue, $cfg ['jumpaction']));
                $pJump .= '}return false;}" />';
                break;
            case 'select' :
                $pJumpValue = "this.options[this.selectedIndex].value";
                $pJump = '<select ' . ($cfg ['jumpplus'] == '' ? ' ' . $cfg ['jumpplus'] . ' onchange="javascript:' : $cfg ['jumpplus']);
                $pJump .= ($cfg ['jumpaction'] == '' ? ((strtolower(substr($cfg ['link'], 0, 11)) == 'javascript:') ? str_replace('*', $pJumpValue, substr($cfg ['link'], 12)) : " document.location.href='" . str_replace('*', '\'+' . $pJumpValue . '+\'', $cfg ['link']) . '\';') : str_replace("*", $pJumpValue, $cfg ['jumpaction']));
                $pJump .= '" title="请选择要跳转到的页数"> ';
                if ($cfg ['jumplong'] == 0) {
                    for ($i = 0; $i <= $cfg ['pagecount']; $i++) {
                        $pJump .= '<option value="' . $i . '"' . (($i == $cfg ['pageindex']) ? ' selected="selected"' : '') . ' >' . $i . '</option> ';
                    }
                } else {
                    $pJumpLong = intval($cfg ['jumplong'] / 2);
                    $pJumpStart = ((($cfg ['pageindex'] - $pJumpLong) < 1) ? 1 : ($cfg ['pageindex'] - $pJumpLong));
                    $pJumpStart = ((($cfg ['pagecount'] - $cfg ['pageindex']) < $pJumpLong) ? ($pJumpStart - ($pJumpLong - ($cfg ['pagecount'] - $cfg ['pageindex'])) + 1) : $pJumpStart);
                    $pJumpStart = (($pJumpStart < 1) ? 1 : $pJumpStart);
                    $j = 1;
                    for ($i = $pJumpStart; $i <= $cfg ['pageindex']; $i++, $j++) {
                        $pJump .= '<option value="' . $i . '"' . (($i == $cfg ['pageindex']) ? ' selected="selected"' : '') . '>' . $i . '</option> ';
                    }
                    $pJumpLong = $cfg ['pagecount'] - $cfg ['pageindex'] < $pJumpLong ? $pJumpLong : $pJumpLong + ($pJumpLong - $j) + 1;
                    $pJumpEnd = $cfg ['pageindex'] + $pJumpLong > $cfg ['pagecount'] ? $cfg ['pagecount'] : $cfg ['pageindex'] + $pJumpLong;
                    for ($i = $cfg ['pageindex'] + 1; $i <= $pJumpEnd; $i++) {
                        $pJump .= '<option value="' . $i . '">' . $i . '</option> ';
                    }
                }
                $pJump .= '</select>';
                break;
        }
        $patterns = array('/{recordcount}/', '/{pagecount}/', '/{pageindex}/', '/{pagesize}/', '/{list}/', '/{liststart}/', '/{listend}/', '/{first}/', '/{prev}/', '/{next}/', '/{last}/', '/{jump}/');
        $replace = array($cfg ['recordcount'], $cfg ['pagecount'], $cfg ['pageindex'], $cfg ['pagesize'], $pList, $pListStart, $pListEnd, $pFirst, $pPrev, $pNext, $pLast, $pJump);
        $tmpStr = chr(13) . chr(10) . preg_replace($patterns, $replace, $tmpStr) . chr(13) . chr(10);
        unset($cfg);
        return $tmpStr;
    }

}

