<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ProjectFuncdot extends Model
{
    const STATUS_ON = 1;
    //指定表名
    protected $table = 'project_funcdot';

    //获取模块
    public function getModels()
    {
        return $this->hasOne('App\models\ProjectModel', 'id', 'model_id');
    }
    //定义功能分类表的关系：一对一
    public function FunctypeFid()
    {
        return $this->hasOne('App\models\ProjectFunctype', 'id', 'functype_id');
    }

}
