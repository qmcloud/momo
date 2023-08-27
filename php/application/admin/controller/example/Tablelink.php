<?php

namespace app\admin\controller\example;

use app\common\controller\Backend;

/**
 * 表格联动
 * 点击左侧日志列表，右侧的表格数据会显示指定管理员的日志列表
 * @icon fa fa-table
 */
class Tablelink extends Backend
{
    protected $model = null;
    protected $noNeedRight = ['table1', 'table2'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AdminLog');
    }


    public function table1()
    {
        $this->model = model('Admin');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->field('id,username')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->field('id,username')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch('index');
    }

    public function table2()
    {
        $this->model = model('AdminLog');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch('index');
    }
}
