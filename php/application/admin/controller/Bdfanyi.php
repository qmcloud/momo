<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 百度翻译管理
 *
 * @icon fa fa-circle-o
 */
class Bdfanyi extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }
    
}
