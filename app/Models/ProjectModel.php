<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model
{
    const STATUS_ON = 1;
    //指定表名
    protected $table = 'project_model';

    //定义功能分类表的关系：一对一
	public function FunctypeFid()
	{
		return $this->hasOne('App\models\ProjectFunctype', 'id', 'fid');
	}

}
