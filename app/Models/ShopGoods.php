<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopGoods extends Model
{
    const STATE_ON_SALE = 1;// 上架中
    const STATE_NOT_SALE = 0;// 已下架

    const STATE_NOT_DELETE = 0;// 正常
    const STATE_DELETE = 1;// 已删除

    const STATE_SALE_LIMIT = 1;// 限购商品
    const STATE_SALE_NOT_LIMIT = 0;// 非限购商品

    const STATE_SALE_RECOMMEND = 1;// 推荐商品
    const STATE_SALE_NOT_RECOMMEND = 0;// 非推荐商品

    const STATE_SALE_NEW = 1;// 新品
    const STATE_SALE_NOT_NEW = 0;// 非新品

    const STATE_VIP = 1;// 会员专属
    const STATE_NOT_VIP = 0;// 非会员专属

    const TYPE_NOMAL = 1;// 正常商品
    const TYPE_OTHNER = 2;// 生成的商品

    const STATE_ON_SALE_STRING = '上架中';
    const STATE_NOT_SALE_STRING = '已下架';

    const STATE_NOT_DELETE_STRING = '正常';
    const STATE_DELETE_STRING = '已删除';

    const STATE_SALE_LIMIT_STRING = '限购商品';
    const STATE_SALE_NOT_LIMIT_STRING = '非限购商品';

    const STATE_SALE_RECOMMEND_STRING = '推荐商品';
    const STATE_SALE_NOT_RECOMMEND_STRING = '非推荐商品';

    const STATE_SALE_NEW_STRING = '新品';
    const STATE_SALE_NOT_NEW_STRING = '非新品';

    const STATE_VIP_STRING = '会员专属';
    const STATE_NOT_VIP_STRING = '非会员专属';

    //
    public function shop_category()
    {
        return $this->belongsTo(ShopCategory::class);
    }

    public function products()
    {
        return $this->hasMany(ShopProduct::class, 'goods_id');
    }

    public function checked_products()
    {
        return $this->hasOne(ShopProduct::class, 'goods_id');
    }
    public function bargain()
    {
        return $this->hasOne(ActivityBargain::class, 'goods_id');
    }


    public function specifications()
    {
        return $this->hasMany(ShopGoodsSpecification::class, 'goods_id');
    }

    public function goods_attribute()
    {
        return $this->hasMany(ShopGoodsAttribute::class, 'goods_id');
    }

    // 是否上架
    public static function getSaleDispayMap()
    {
        return [
            self::STATE_ON_SALE => self::STATE_ON_SALE_STRING,
            self::STATE_NOT_SALE => self::STATE_NOT_SALE_STRING,
        ];
    }

    // 商品删除状态
    public static function getDeleteDispayMap()
    {
        return [
            self::STATE_NOT_DELETE => self::STATE_NOT_DELETE_STRING,
            self::STATE_DELETE => self::STATE_DELETE_STRING,
        ];
    }

    // 是否限购
    public static function getLimitDispayMap()
    {
        return [
            self::STATE_SALE_LIMIT => self::STATE_SALE_LIMIT_STRING,
            self::STATE_SALE_NOT_LIMIT => self::STATE_SALE_NOT_LIMIT_STRING,
        ];
    }

    // 是否推荐
    public static function getRecommendDispayMap()
    {
        return [
            self::STATE_SALE_RECOMMEND => self::STATE_SALE_RECOMMEND_STRING,
            self::STATE_SALE_NOT_RECOMMEND => self::STATE_SALE_NOT_RECOMMEND_STRING,
        ];
    }

    // 是否新品
    public static function getNewDispayMap()
    {
        return [
            self::STATE_SALE_NEW => self::STATE_SALE_NEW_STRING,
            self::STATE_SALE_NOT_NEW => self::STATE_SALE_NOT_NEW_STRING,
        ];
    }

    // 是否是会员专属
    public static function getVipDispayMap()
    {
        return [
            self::STATE_VIP => self::STATE_VIP_STRING,
            self::STATE_NOT_VIP => self::STATE_NOT_VIP_STRING,
        ];
    }

    // 多图上传处理
    public function getListPicUrlAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setListPicUrlAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['list_pic_url'] = json_encode($pictures);
        }
    }

    // 获取商品列表
    public static function getGoodsList($where = [], $pagesize = '', $order = 'sort_order asc')
    {
        $model = static::where(array_merge([
//            ['is_delete', '=', static::STATE_NOT_DELETE],
//            ['is_on_sale', '=', static::STATE_ON_SALE],
    ], $where))->orderByRaw($order);
        if ($pagesize) {
            return $model->paginate($pagesize);
        }
        return $model->get();
    }

    // 获取商品详情
    public static function getGoodsDetail($where, $product_id = 0)
    {
        if ($product_id) {
            return static::with(['checked_products' => function ($query) use ($product_id) {
                $query->where('id', '=', $product_id);
            }])->where(array_merge([
                ['is_delete', '=', static::STATE_NOT_DELETE],
                ['is_on_sale', '=', static::STATE_ON_SALE],
            ], $where))->first();
        }
        return static::where(array_merge([
            ['is_delete', '=', static::STATE_NOT_DELETE],
            ['is_on_sale', '=', static::STATE_ON_SALE],
        ], $where))->first();
    }
}
