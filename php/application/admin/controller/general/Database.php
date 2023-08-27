<?php

namespace app\admin\controller\general;

use addons\database\library\Backup;
use app\common\controller\Backend;
use think\Db;
use think\Debug;
use think\Exception;
use think\exception\PDOException;
use ZipArchive;

/**
 * 数据库管理
 *
 * @icon   fa fa-database
 * @remark 可在线进行一些简单的数据库表优化或修复,查看表结构和数据。也可以进行SQL语句的操作
 */
class Database extends Backend
{
    protected $noNeedRight = ['backuplist'];

    public function _initialize()
    {
        if (!config("app_debug")) {
            $this->error("数据库管理插件只允许在开发环境下使用");
        }
        return parent::_initialize();
    }

    /**
     * 查看
     */
    public function index()
    {
        $tables_data_length = $tables_index_length = $tables_free_length = $tables_data_count = 0;
        $tables = $list = [];
        $list = Db::query("SHOW TABLES");
        foreach ($list as $key => $row) {
            $tables[] = ['name' => reset($row), 'rows' => 0];
        }
        $data['tables'] = $tables;
        $data['saved_sql'] = [];
        $this->view->assign($data);
        return $this->view->fetch();
    }

    /**
     * SQL查询
     */
    public function query()
    {
        $do_action = $this->request->post('do_action');

        echo '<style type="text/css">
            xmp,body{margin:0;padding:0;line-height:18px;font-size:13px;font-family:"Helvetica Neue", Helvetica, Microsoft Yahei, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif;}
            hr{height:1px;margin:5px 1px;background:#e3e3e3;border:none;}
            </style>';
        if ($do_action == '') {
            exit(__('Invalid parameters'));
        }

        $tablename = $this->request->post("tablename/a");

        if (in_array($do_action, array('doquery', 'optimizeall', 'repairall'))) {
            $this->$do_action();
        } elseif (count($tablename) == 0) {
            exit(__('Invalid parameters'));
        } else {
            foreach ($tablename as $table) {
                $this->$do_action($table);
                echo "<br />";
            }
        }
    }

    /**
     * 备份列表
     * @internal
     */
    public function backuplist()
    {
        $config = get_addon_config('database');
        $backupDir = ROOT_PATH . 'public' . DS . $config['backupDir'];

        $backuplist = [];
        foreach (glob($backupDir . "*.zip") as $filename) {
            $time = filemtime($filename);
            $backuplist[$time] =
                [
                    'file' => str_replace($backupDir, '', $filename),
                    'date' => date("Y-m-d H:i:s", $time),
                    'size' => format_bytes(filesize($filename))
                ];
        }
        krsort($backuplist);

        $this->success("", null, ['backuplist' => array_values($backuplist)]);
    }

    /**
     * 还原
     */
    public function restore($ids = '')
    {
        $config = get_addon_config('database');
        $backupDir = ROOT_PATH . 'public' . DS . $config['backupDir'];
        if ($this->request->isPost()) {
            $action = $this->request->request('action');
            $file = $this->request->request('file');
            if (!preg_match("/^backup\-([a-z0-9\-]+)\.zip$/i", $file)) {
                $this->error(__("Invalid parameters"));
            }
            $file = $backupDir . $file;
            if ($action == 'restore') {
                if (!class_exists('ZipArchive')) {
                    $this->error("服务器缺少php-zip组件，无法进行还原操作");
                }
                try {
                    $dir = RUNTIME_PATH . 'database' . DS;
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755);
                    }

                    $zip = new ZipArchive;
                    if ($zip->open($file) !== true) {
                        throw new Exception(__('Can not open zip file'));
                    }
                    if (!$zip->extractTo($dir)) {
                        $zip->close();
                        throw new Exception(__('Can not unzip file'));
                    }
                    $zip->close();
                    $filename = basename($file);
                    $sqlFile = $dir . str_replace('.zip', '.sql', $filename);
                    if (!is_file($sqlFile)) {
                        throw new Exception(__('Sql file not found'));
                    }
                    $filesize = filesize($sqlFile);
                    $list = Db::query('SELECT @@global.max_allowed_packet');
                    if (isset($list[0]['@@global.max_allowed_packet']) && $filesize >= $list[0]['@@global.max_allowed_packet']) {
                        Db::execute('SET @@global.max_allowed_packet = ' . ($filesize + 1024));
                        //throw new Exception('备份文件超过配置max_allowed_packet大小，请修改Mysql服务器配置');
                    }
                    $sql = file_get_contents($sqlFile);

                    Db::clear();
                    //必须重连一次
                    Db::connect([], true)->query("select 1");
                    Db::getPdo()->exec($sql);
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                $this->success(__('Restore successful'));
            } elseif ($action == 'delete') {
                unlink($file);
                $this->success(__('Delete successful'));
            }
        }
    }

    /**
     * 备份
     */
    public function backup()
    {
        $config = get_addon_config('database');
        $backupDir = ROOT_PATH . 'public' . DS . $config['backupDir'];
        if ($this->request->isPost()) {
            if (!class_exists('ZipArchive')) {
                $this->error("服务器缺少php-zip组件，无法进行备份操作");
            }
            $database = config('database');
            try {
                $backup = new Backup($database['hostname'], $database['username'], $database['database'], $database['password'], $database['hostport']);
                $backup->setIgnoreTable($config['backupIgnoreTables'])->backup($backupDir);
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success(__('Backup successful'));
        }
        return;
    }

    private function viewinfo($name)
    {
        $row = Db::query("SHOW CREATE TABLE `{$name}`");
        $row = array_values($row[0]);
        $info = $row[1];
        echo "<xmp>{$info};</xmp>";
    }

    private function viewdata($name = '')
    {
        $sqlquery = "SELECT * FROM `{$name}`";
        $this->doquery($sqlquery);
    }

    private function optimize($name = '')
    {
        if (Db::execute("OPTIMIZE TABLE `{$name}`")) {
            echo __('Optimize table %s done', $name);
        } else {
            echo __('Optimize table %s fail', $name);
        }
    }

    private function optimizeall($name = '')
    {
        $list = Db::query("SHOW TABLES");
        foreach ($list as $key => $row) {
            $name = reset($row);
            if (Db::execute("OPTIMIZE TABLE {$name}")) {
                echo __('Optimize table %s done', $name);
            } else {
                echo __('Optimize table %s fail', $name);
            }
            echo "<br />";
        }
    }

    private function repair($name = '')
    {
        if (Db::execute("REPAIR TABLE `{$name}`")) {
            echo __('Repair table %s done', $name);
        } else {
            echo __('Repair table %s fail', $name);
        }
    }

    private function repairall($name = '')
    {
        $list = Db::query("SHOW TABLES");
        foreach ($list as $key => $row) {
            $name = reset($row);
            if (Db::execute("REPAIR TABLE {$name}")) {
                echo __('Repair table %s done', $name);
            } else {
                echo __('Repair table %s fail', $name);
            }
            echo "<br />";
        }
    }

    private function doquery($sql = null)
    {
        $sqlquery = $sql ? $sql : $this->request->post('sqlquery');
        if ($sqlquery == '') {
            exit(__('SQL can not be empty'));
        }
        $sqlquery = str_replace('__PREFIX__', config('database.prefix'), $sqlquery);
        $sqlquery = str_replace("\r", "", $sqlquery);
        $sqls = preg_split("/;[ \t]{0,}\n/i", $sqlquery);
        $maxreturn = 100;
        $r = '';
        foreach ($sqls as $key => $val) {
            if (trim($val) == '') {
                continue;
            }
            $val = rtrim($val, ';');
            $r .= "SQL：<span style='color:green;'>{$val}</span> ";
            if (preg_match("/^(select|explain)(.*)/i ", $val)) {
                Debug::remark("begin");
                $limit = stripos(strtolower($val), "limit") !== false ? true : false;
                try {
                    $count = Db::execute($val);
                    if ($count > 0) {
                        $resultlist = Db::query($val . (!$limit && $count > $maxreturn ? ' LIMIT ' . $maxreturn : ''));
                    } else {
                        $resultlist = [];
                    }
                } catch (\PDOException $e) {
                    continue;
                }
                Debug::remark("end");
                $time = Debug::getRangeTime('begin', 'end', 4);

                $usedseconds = __('Query took %s seconds', $time) . "<br />";
                if ($count <= 0) {
                    $r .= __('Query returned an empty result');
                } else {
                    $r .= (__('Total:%s', $count) . (!$limit && $count > $maxreturn ? ',' . __('Max output:%s', $maxreturn) : ""));
                }
                $r = $r . ',' . $usedseconds;
                $j = 0;
                foreach ($resultlist as $m => $n) {
                    $j++;
                    if (!$limit && $j > $maxreturn) {
                        break;
                    }
                    $r .= "<hr/>";
                    $r .= "<font color='red'>" . __('Row:%s', $j) . "</font><br />";
                    foreach ($n as $k => $v) {
                        $r .= "<font color='blue'>{$k}：</font>{$v}<br/>\r\n";
                    }
                }
            } else {
                try {
                    Debug::remark("begin");
                    $count = Db::getPdo()->exec($val);
                    Debug::remark("end");

                } catch (\PDOException $e) {
                    continue;
                }
                $time = Debug::getRangeTime('begin', 'end', 4);
                $r .= __('Query affected %s rows and took %s seconds', $count, $time) . "<br />";
            }
        }
        echo $r;
    }
}
