<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Lang extends Backend
{

    /**
     * Lang模型对象
     * @var \app\admin\model\Lang
     */
    protected $model = null;
    protected $LangDir = null;

    public function _initialize()
    {

        parent::_initialize();
        $adminPath = dirname(__DIR__) . DS;
        $this->LangDir = $adminPath . 'lang' . DS;

        $this->model = new \app\admin\model\Lang;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $lang = json_decode($params['lang_json'], true);
                    if (empty($lang)) {
                        $this->error();
                    }
                    $LangFile = $params['lang_file'];
                    $raw_lang_data = $this->start_lang($LangFile);
                    $lang_data = [];
                    foreach ($lang as $vo) {
                        $lang_data[$vo['key']] = $vo['value'];
                    }
                    // 返回 array 格式字符串
                    $lang_var = $this->var_export_short($lang_data, 4);
                    // lang 模板
                    $lang_tlp = <<<EOT
<?php
                    
return {$lang_var};
EOT;
                    // 写入lang文件
                    $put_res = file_put_contents($this->LangDir . $LangFile, $lang_tlp);
                    if (!$put_res) {
                        throw new Exception('文件写入失败 请确定lang目录有写入权限');
                    }

                    $save = [
                        'file_name'     => $LangFile,
                        'lang_json'     => json_encode($lang),
                        'raw_lang_json' => json_encode($raw_lang_data),
                    ];
                    $result = $this->model->allowField(true)->save($save);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 获取语言文件列表
     * @internal
     */
    public function get_controller_list()
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->LangDir), \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $list = [];
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $name = str_replace($this->LangDir, '', $filePath);
                $name = str_replace(DS, "/", $name);
                $list[] = ['id' => $name, 'name' => $name];
            }
        }
        $pageNumber = $this->request->request("pageNumber");
        $pageSize = $this->request->request("pageSize");
        return json(['list' => array_slice($list, ($pageNumber - 1) * $pageSize, $pageSize), 'total' => count($list)]);
    }

    public function get_lang_content()
    {
        if ($this->request->isAjax()) {
            $lang_file = input('lang_file');
            $lang = $this->start_lang($lang_file);
            $this->success('语言文件数据加载成功', '', $lang);
        }

    }

    protected function var_export_short($var, $indent = "")
    {
        if (gettype($var) != 'array') {
            $this->error('语言变量不为array类');
        }
        $indexed = array_keys($var) === range(0, count($var) - 1);
        $r = [];
        foreach ($var as $key => $value) {
            $r[] = "    "
                . ($indexed ? "" : var_export_short($key) . " => ")
                . var_export_short($value, "    ");
        }
        return "[\n" . implode(",\n", $r) . "\n" . "]";
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function start_lang($name)
    {
        try {
            if (strstr($name, '../') || strstr($name, '..\\')) {
                throw new Exception("文件目录有误!!!");
            }
            $lang_var = require $this->LangDir . $name;
            if (gettype($lang_var) != 'array') {
                $this->error('加载文件有误~!!!');
            }
            $lang_data = [];
            foreach ($lang_var as $key => $vo) {
                $lang_data[] = [
                    'key'   => $key,
                    'value' => $vo,
                ];
            }
            return $lang_data;
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }


}
