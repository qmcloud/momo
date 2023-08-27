<?php

namespace addons\example\controller;

use think\addons\Controller;

/**
 * 测试控制器
 */
class Demo extends Controller
{

    protected $layout = 'default';
    protected $noNeedLogin = ['index', 'demo1'];
    protected $noNeedRight = ['*'];

    public function index()
    {
        return $this->view->fetch();
    }

    public function demo1()
    {
        return $this->view->fetch();
    }

    public function demo2()
    {
        return $this->view->fetch();
    }

}
