<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    use ModelTree, AdminBuilder;

    const STATE_INACTIVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE_STRING = '禁用';
    const STATE_ACTIVE_STRING = '正常';

    //
    protected $table = "shop_category";

    protected $fillable = ['name', 'keywords', 'front_desc', 'parent_id', 'sort_order', 'is_show', 'banner_url', 'icon_url', 'img_url', 'level'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('parent_id');
        $this->setOrderColumn('sort_order');
        $this->setTitleColumn('name');
    }

    public function shop_goods()
    {
        return $this->hasMany(ShopGoods::class);
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
        $classes = static::all(['id', 'name']);

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
            $result[$eachClass->id] = $eachClass->name;
        }

        return $result;
    }

    public static function getCategoryList($where= []){
        return static::where(array_merge([
            ['is_show', '=', static::STATE_ACTIVE],
        ], $where))->orderBy('sort_order')->get();
    }

    public static function getParentCategory($where= []){
        $where = array_merge([
            ['level', '=', 0]
        ], $where);
        return static::getCategoryList($where);
    }

    public static function getChildrenCategoryByParentId($parentId){
        return static::where([
            ['is_show', '=', static::STATE_ACTIVE],
            ['parent_id', '=', $parentId],
        ])->orderBy('sort_order')->get();
    }

}
