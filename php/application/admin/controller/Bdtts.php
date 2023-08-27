<?php
namespace app\admin\controller;
use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 百度语音合成管理
 *
 * @icon fa fa-circle-o
 */
class Bdtts extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        // $this->model = model('Bdtts');
    }
    
}
