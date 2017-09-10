<?php
// +----------------------------------------------------------------------
// | QQ群274904994 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 51zhibo.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: 51zhibo.top
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 后台默认控制器
 * @author 51zhibo.top
 */
class IndexController extends AdminController
{
    /**
     * 默认方法
     * @author 51zhibo.top
     */
    public function index()
    {
        $this->assign('meta_title', "首页");
        $this->display();
    }

    /**
     * 删除缓存
     * @author 51zhibo.top
     */
    public function removeRuntime()
    {
        $file   = new \Util\File();
        $result = $file->del_dir(RUNTIME_PATH);
        if ($result) {
            $this->success("缓存清理成功");
        } else {
            $this->error("缓存清理失败");
        }
    }
}
