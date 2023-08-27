<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Addonmask extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function valid()
    {
        $this->success('请求成功');
    }
}
