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
namespace Common\Behavior;
use Think\Behavior;
use Think\Hook;

// 初始化钩子信息
class InitHookBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(isset($_GET['g']) && strtolower($_GET['g']) === 'install') return;
        
        $data = S('hooks');
        if(!$data){
           is_array($plugins = M('Plugins')->where("status=1")->getField("name,hooks"))?null:$plugins = array();
           foreach ($plugins as $plugin => $hooks) {
                if($hooks){
                	$hooks=explode(",", $hooks);
                	foreach ($hooks as $hook){
                		Hook::add($hook,$plugin);
                	}
                }
            }
            S('hooks',Hook::get());
        }else{
           Hook::import($data,false);
        }
    }
}