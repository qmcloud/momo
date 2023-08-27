<?php

namespace addons\example\controller;

use think\addons\Controller;

class Index extends Controller
{

    protected $layout = 'default';

    public function index()
    {
        return $this->view->fetch();
    }

}
