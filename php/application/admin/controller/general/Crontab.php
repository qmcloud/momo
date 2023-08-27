<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;
use Cron\CronExpression;

/**
 * 定时任务
 *
 * @icon   fa fa-tasks
 * @remark 按照设定的时间进行任务的执行,目前支持三种任务:请求URL、执行SQL、执行Shell。
 */
class Crontab extends Backend
{

    protected $model = null;
    protected $noNeedRight = ['check_schedule', 'get_schedule_future'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Crontab');
        $this->view->assign('typeList', \app\admin\model\Crontab::getTypeList());
        $this->assignconfig('typeList', \app\admin\model\Crontab::getTypeList());
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
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
            $time = time();
            foreach ($list as $k => &$v) {
                $cron = CronExpression::factory($v['schedule']);
                $v['nexttime'] = $time > $v['endtime'] ? __('None') : $cron->getNextRunDate()->getTimestamp();
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 判断Crontab格式是否正确
     * @internal
     */
    public function check_schedule()
    {
        $row = $this->request->post("row/a");
        $schedule = isset($row['schedule']) ? $row['schedule'] : '';
        if (CronExpression::isValidExpression($schedule)) {
            $this->success();
        } else {
            $this->error(__('Crontab format invalid'));
        }
    }

    /**
     * 根据Crontab表达式读取未来七次的时间
     * @internal
     */
    public function get_schedule_future()
    {
        $time = [];
        $schedule = $this->request->post('schedule');
        $days = (int)$this->request->post('days');
        try {
            $cron = CronExpression::factory($schedule);
            for ($i = 0; $i < $days; $i++) {
                $time[] = $cron->getNextRunDate(null, $i)->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {

        }

        $this->success("", null, ['futuretime' => $time]);
    }

}
