<?php

namespace addons\crontab\controller;

use addons\crontab\model\Crontab;
use Cron\CronExpression;
use fast\Http;
use think\Controller;
use think\Db;
use think\Exception;
use think\Log;

/**
 * 定时任务接口
 *
 * 以Crontab方式每分钟定时执行,且只可以Cli方式运行
 * @internal
 */
class Autotask extends Controller
{

    /**
     * 初始化方法,最前且始终执行
     */
    public function _initialize()
    {
        // 只可以以cli方式执行
        if (!$this->request->isCli()) {
            $this->error('Autotask script only work at client!');
        }

        parent::_initialize();

        // 清除错误
        error_reporting(0);

        // 设置永不超时
        set_time_limit(0);
    }

    /**
     * 执行定时任务
     */
    public function index()
    {
        $time = time();
        $logDir = LOG_PATH . 'crontab' . DS;
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755);
        }
        //筛选未过期且未完成的任务
        $crontabList = Crontab::where('status', '=', 'normal')->order('weigh DESC,id DESC')->select();
        $execTime = time();
        foreach ($crontabList as $crontab) {
            $update = [];
            $execute = false;
            if ($time < $crontab['begintime']) {
                //任务未开始
                continue;
            }
            if ($crontab['maximums'] && $crontab['executes'] > $crontab['maximums']) {
                //任务已超过最大执行次数
                $update['status'] = 'completed';
            } else {
                if ($crontab['endtime'] > 0 && $time > $crontab['endtime']) {
                    //任务已过期
                    $update['status'] = 'expired';
                } else {
                    //重复执行
                    //如果未到执行时间则继续循环
                    $cron = CronExpression::factory($crontab['schedule']);
                    if (!$cron->isDue(date("YmdHi", $execTime)) || date("YmdHi", $execTime) === date("YmdHi", $crontab['executetime'])) {
                        continue;
                    }
                    $execute = true;
                }
            }

            // 如果允许执行
            if ($execute) {
                $update['executetime'] = $time;
                $update['executes'] = $crontab['executes'] + 1;
                $update['status'] = ($crontab['maximums'] > 0 && $update['executes'] >= $crontab['maximums']) ? 'completed' : 'normal';
            }

            // 如果需要更新状态
            if (!$update) {
                continue;
            }
            // 更新状态
            $crontab->save($update);

            // 将执行放在后面是为了避免超时导致多次执行
            if (!$execute) {
                continue;
            }
            $result = false;
            $message = '';

            try {
                if ($crontab['type'] == 'url') {
                    if (substr($crontab['content'], 0, 1) == "/") {
                        // 本地项目URL
                        $message = shell_exec('php ' . ROOT_PATH . 'public/index.php ' . $crontab['content']);
                        $result = $message ? true : false;
                    } else {
                        $arr = explode(" ", $crontab['content']);
                        $url = $arr[0];
                        $params = isset($arr[1]) ? $arr[1] : '';
                        $method = isset($arr[2]) ? $arr[2] : 'POST';
                        try {
                            // 远程异步调用URL
                            $ret = Http::sendRequest($url, $params, $method);
                            $result = $ret['ret'];
                            $message = $ret['msg'];
                        } catch (\Exception $e) {
                            $message = $e->getMessage();
                        }
                    }

                } elseif ($crontab['type'] == 'sql') {
                    $ret = $this->sql($crontab['content']);
                    $result = $ret['ret'];
                    $message = $ret['msg'];
                } elseif ($crontab['type'] == 'shell') {
                    // 执行Shell
                    $message = shell_exec($crontab['content']);
                    $result = $message ? true : false;
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
            $log = [
                'crontab_id'   => $crontab['id'],
                'executetime'  => $time,
                'completetime' => time(),
                'content'      => $message,
                'status'       => $result ? 'success' : 'failure',
            ];
            Db::name("crontab_log")->insert($log);
        }
        return "Execute completed!\n";
    }

    /**
     * 执行SQL语句
     */
    protected function sql($sql)
    {
        //这里需要强制重连数据库,使用已有的连接会报2014错误
        $connect = Db::connect([], true);
        $connect->execute("select 1");

        // 执行SQL
        $sqlquery = str_replace('__PREFIX__', config('database.prefix'), $sql);
        $sqls = preg_split("/;[ \t]{0,}\n/i", $sqlquery);

        $result = false;
        $message = '';
        $connect->startTrans();
        try {
            foreach ($sqls as $key => $val) {
                if (trim($val) == '' || substr($val, 0, 2) == '--' || substr($val, 0, 2) == '/*') {
                    continue;
                }
                $message .= "\nSQL:{$val}\n";
                $val = rtrim($val, ';');
                if (preg_match("/^(select|explain)(.*)/i ", $val)) {
                    $count = $connect->execute($val);
                    if ($count > 0) {
                        $resultlist = Db::query($val);
                    } else {
                        $resultlist = [];
                    }

                    $message .= "Total:{$count}\n";
                    $j = 1;
                    foreach ($resultlist as $m => $n) {
                        $message .= "\n";
                        $message .= "Row:{$j}\n";
                        foreach ($n as $k => $v) {
                            $message .= "{$k}：{$v}\n";
                        }
                        $j++;
                    }
                } else {
                    $count = $connect->getPdo()->exec($val);
                    $message = "Affected rows:{$count}";
                }
            }
            $connect->commit();
            $result = true;
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $connect->rollback();
            $result = false;
        }
        return ['ret' => $result, 'msg' => $message];
    }
}
