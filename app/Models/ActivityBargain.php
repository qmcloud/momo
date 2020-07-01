<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityBargain extends Model
{
    const STATE_ON = 1;
    const STATE_OFF = 0;
    const STATE_ON_STRING = '正常';
    const STATE_OFF_STRING = '下架';

    //
    protected $table = "activity_bargain";


    public function goods()
    {
        return $this->hasOne(ShopGoods::class, 'id', 'goods_id');
    }

    public static function getStateDisplayMap()
    {
        return [
            self::STATE_ON => self::STATE_ON_STRING,
            self::STATE_OFF => self::STATE_OFF_STRING
        ];
    }

    // 多图上传处理
    public function getImagesAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setImagesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['images'] = json_encode($pictures);
        }
    }

    static public function getValidList($where = [], $pagesize = '', $order = 'sort asc')
    {
        $model = static::with('goods')->where(array_merge([
            ['status', '=', static::STATE_ON],
        ], $where))->orderByRaw($order);
        if ($pagesize) {
            return $model->paginate($pagesize);
        }
        return $model->get();
    }


}
