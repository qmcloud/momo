<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShopComment extends Model
{
    const STATE_ON = 1;
    const STATE_OFF = 0;
    const STATE_ON_STRING = '正常';
    const STATE_OFF_STRING = '下架';

    //
    protected $table = "shop_comment";

    public function shop_goods()
    {
        return $this->hasOne(ShopGoods::class,'value_id');
    }

    public function shop_product()
    {
        return $this->hasOne(ShopProduct::class,'product_id');
    }

    public function get_comment_picture()
    {
        return $this->hasMany(ShopCommentPicture::class,'comment_id');
    }

    public static function getStateDisplayMap()
    {
        return [
            self::STATE_ON => self::STATE_ON_STRING,
            self::STATE_OFF => self::STATE_OFF_STRING
        ];
    }

    // 获取评论列表
    public static function getCommentList($where= [],$pagesize=''){
        $model =  static::with('get_comment_picture')->where(array_merge([
            ['status', '=', static::STATE_ON],
        ], $where))->orderBy('sort_order');
        if($pagesize){
            return $model->paginate($pagesize);
        }
        return $model->get();
    }

    // 获取有图评论列表
    public static function getCommentListPics($where= [],$pagesize=''){
        $model =  static::has('get_comment_picture')->where(array_merge([
            ['status', '=', static::STATE_ON],
        ], $where))->orderBy('sort_order');
        if($pagesize){
            return $model->paginate($pagesize);
        }
        return $model->get();
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            // Leave this part off if you want to keep the property as
            // a Carbon object rather than always just returning a string
            ->toDateTimeString()
            ;
    }
}
