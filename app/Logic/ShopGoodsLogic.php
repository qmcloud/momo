<?php
/**
 * sqc @小T科技 2018.03.06
 *
 *
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Resources\ShopGoods as ShopGoodsResource;
use App\Models\ShopGoods;
use App\Models\ShopCategory;
use App\Models\ShopFootprint;
use App\Http\Resources\ShopCategory as ShopCategoryResource;
use App\Models\ShopGoodsAttribute;
use App\Http\Resources\ShopGoodsAttribute as ShopGoodsAttributeResource;
use App\Models\ShopGoodsIssue;
use App\Models\ShopCollect;
use Illuminate\Support\Carbon;
use App\Models\ShopBrand;

class ShopGoodsLogic
{

    public function __construct()
    {

    }

    // 获取商品全部信息
    static public function getFullGoodsInfo($id){
        $goodsInfo = static::getGoodsDetail(['id' => $id]);
        $goods_where = ['goods_id'=>$goodsInfo->id];
        $attribute = static::getGoodsAttribute($goods_where);
        $issueList = static::getGoodsIssue($goods_where);
        $comment = ShopCommentLogic::getCommentList(['value_id' =>$goodsInfo->id],0,10);
        $brand = ShopBrand::getDetail(['id'=>$goodsInfo->brand_id]);
        $userHasCollect = ShopGoodsLogic::userCollectStatus($goodsInfo->id);
        ShopGoodsLogic::addFootprint($goodsInfo->id);
        $outData = [
            'info' => $goodsInfo,                    // 商品信息
            'attribute' => $attribute,              // 商品属性参数
            'issue' => $issueList,                  // 商品问题
            'comment' => ['data'=>$comment,'count' => $comment->total()],                  // 商品评论
            'brand' => $brand,                      // 品牌信息
            'specificationList' => [],            // 规格信息  后期完善该项 2018.6.12
            'productList' => [],                   // sku列表   后期完善该项 2018.6.12
            'userHasCollect' => $userHasCollect,   // 是否收藏
        ];
        return $outData;
    }

    // 统计商品
    static public function getGoodsCount($where)
    {
        return ShopGoods::where($where)->count();
    }

    // 获取商品分类
    static public function getGoodsCategory($where)
    {
        $currentCategory = ShopCategory::where($where)->first();
        if (empty($currentCategory)) {
            return false;
        }
        $brotherWhere = ['parent_id' => $currentCategory['parent_id']];
        $brotherCategory = ShopCategory::getCategoryList($brotherWhere);
        return [
            'brotherCategory' => ShopCategoryResource::collection($brotherCategory),
            'currentCategory' => new ShopCategoryResource($currentCategory)
        ];
    }

    // 获取商品列表
    static public function getGoodsList($where, $pagesize = '', $order = 'sort_order asc')
    {
        visits('App\Models\ShopGoods', 'list')->Increment();// 访问统计 see:https://github.com/awssat/laravel-visits
        $goodsList = ShopGoods::getGoodsList($where, $pagesize, $order);
        if (!$goodsList) {
            return false;
        }
        return ShopGoodsResource::collection($goodsList)->additional(['code' => 200, 'status' => 'success']);
    }

    // 获取商品详情
    static public function getGoodsDetail($where)
    {
        visits('App\Models\ShopGoods', 'detail')->Increment();// 访问统计 see:https://github.com/awssat/laravel-visits
        $goodsDetail = ShopGoods::getGoodsDetail($where);
        return new ShopGoodsResource($goodsDetail);
    }

    // 获取商品的属性参数信息
    static public function getGoodsAttribute($where)
    {
        $goodsAttribute = ShopGoodsAttribute::getGoodsAttribute($where);
        return ShopGoodsAttributeResource::collection($goodsAttribute);
    }

    // 获取商品的问题描述信息
    static public function getGoodsIssue($where)
    {
        $goodsAttribute = ShopGoodsIssue::getGoodsIssue($where);
        return $goodsAttribute;
    }

    // 收藏商品状态查询
    static public function userCollectStatus($goods_id)
    {
        if (empty(\Auth::user()->id)) {
            return 0;
        }
        $user_id = \Auth::user()->id;
        $info = ShopCollect::getCollectDetail([
            'type_id' => 0,
            'value_id' => $goods_id,
            'user_id' => $user_id,
        ]);
        if (empty($info['is_attention'])) {
            return 0;
        }
        return 1;
    }

    // 获取关联商品
    static public function getRelatedGoods($where)
    {
        $goodsList = ShopGoods::where($where)->inRandomOrder()->take(20)->get();
        return ShopGoodsResource::collection($goodsList);
    }

    // 添加商品足迹
    static public function addFootprint($goods_id)
    {
        try {
            if (empty(\Auth::user()->id)) {
                $uid = 0;
            } else {
                $uid = \Auth::user()->id;
            }
            if (empty($goods_id) || empty($uid)) {
                return;
            }
            $newModel = new ShopFootprint();
            $newModel->goods_id = $goods_id;
            $newModel->uid = $uid;
            $newModel->add_time = Carbon::now();
            $newModel->save();
        } catch (\Exception $e) {
            // 足迹报错失败
        }
    }


}
