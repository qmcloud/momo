<?php

namespace app\admin\model;

use think\Model;


class Turntable extends Model
{

    

    

    // 表名
    protected $name = 'turntable';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'uptime_text'
    ];
    

    



    public function getUptimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['uptime']) ? $data['uptime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setUptimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
