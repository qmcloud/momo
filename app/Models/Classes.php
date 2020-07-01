<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use ModelTree, AdminBuilder;

    const STATE_INACTIVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE_STRING = '禁用';
    const STATE_ACTIVE_STRING = '正常';

    //
    protected $table = "classes";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('class_pid');
        $this->setOrderColumn('class_level');
        $this->setTitleColumn('class_name');
    }

    public function goods()
    {
        return $this->hasMany(Good::class);
    }

    public static function getStateDisplayMap()
    {
        return [
            self::STATE_INACTIVE => self::STATE_INACTIVE_STRING,
            self::STATE_ACTIVE => self::STATE_ACTIVE_STRING
        ];
    }

    public static function getStateDisplayConfig()
    {
        return [
            'on' => [
                'text' => self::STATE_ACTIVE_STRING,
            ],
            'off' => [
                'text' =>self::STATE_INACTIVE_STRING,
            ]
        ];
    }

    public static function getAllClasses($noRoot = false)
    {
        $classes = Classes::all(['id', 'class_name']);

        $result = [];

        if (!$noRoot) {
            $result = [
                0 => 'root'
            ];
        } else {
            $result = [
                0 => 'null'
            ];
        }
        foreach ($classes as $eachClass) {
            $result[$eachClass->id] = $eachClass->class_name;
        }

        return $result;
    }
}
