<?php
namespace App\Helpers\Logic;


/**
 * Logic帮助类
 * 使用举例1：
 */
trait HelperLogic
{
    /**
     * 数据组装model
     * @var
     */
    protected $resourceModels = [];

    /**
     * 验证器类
     * @var
     */
    protected $validator = '';

    /**
     * 领取数据model
     * @var
     */
    protected $dataModel = '';
    protected $dataModelClass = '';
    // assoc关联
    protected $assocData = [];

    /**
     * 数据参数验证器类
     * @var
     */
    protected $paramFrom = '';

    /**
     * 提供给外部的设置内部属性的入口
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    // 外部组装数据工具类
    public function setResourceModels($Chip, $key = '')
    {
        if ($key) {
            $this->resourceModels[$key] = $Chip;
        } else {
            $this->resourceModels = $Chip;
        }
        return $this;
    }

    // 设置领取数据model
    public function setDataModels($model)
    {
        if (is_object($model)) {
            $this->dataModel = $model;
            $this->dataModelClass = get_class($model);
        }
        return $this;
    }

    // assoc关联
    public function setAssocData($data)
    {
        if (is_array($data)) {
            $this->assocData = $data;
        }
        return $this;
    }

    // 组装数据参数类
    public function setFromModels($Chip)
    {
        $this->paramFrom = $Chip;
        return $this;
    }


    public function __get($property_name)
    {
        try {
            return $this->$property_name = $this->paramFrom[$property_name];
        } catch (\Exception $e) {
            return $this->$property_name = '';
        }
    }

}