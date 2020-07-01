<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Logic\ShopGoodsLogic;
use App\Logic\ShopCommentLogic;
use App\Models\ShopBrand;
use Illuminate\Support\Facades\Validator;
use App\Models\ShopCategory;
use App\Models\Carousel;

class ShopGoodsController extends ApiController
{
    // 商品统计
    public function getGoodsCount(Request $request)
    {
        $outData = ShopGoodsLogic::getGoodsCount([['id', '>', 0]]);
        return $this->success($outData);
    }

    // 获取商品列表
    public function getGoodsList(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'categoryId' => 'required',
            ],
            [
                'categoryId.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $where = [];
        if( $request->keyword){
            $where[] = ['goods_name', 'like' , '%'.$request->keyword.'%'];
        }
        if( $request->categoryId){
            $where['category_id'] = $request->categoryId;
        }
        // 新品
        if($request->isNew){
            $where['is_new'] = $request->isNew;
        }
        // 热门
        if($request->isHot){
            $where['is_hot'] = $request->isHot;
        }
        // 品牌
        if($request->brandId){
            $where['brand_id'] = $request->brandId;
        }
        $order = '';
        $inputSort = $request->input('sort','default');
        switch($inputSort){
            case 'price':
                $order = 'retail_price '. $request->input('order','asc');
                break;
            default:
                $order = 'sort_order asc';
        }
        $outData = ShopGoodsLogic::getGoodsList($where, $request->size ? $request->size : 10,$order);
        if ($outData) {
            return $outData;
        }
        return $this->success([]);
    }

    // 获取商品分类列表
    public function getGoodsCategory(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $outData = ShopGoodsLogic::getGoodsCategory(['id' => $request->id]);
        return $this->success($outData);
    }


    // 获取商品详情
    public function getGoodsDetail(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $goodsInfo = ShopGoodsLogic::getGoodsDetail(['id' => $request->id]);
        $goods_where = ['goods_id'=>$goodsInfo->id];
        $attribute = ShopGoodsLogic::getGoodsAttribute($goods_where);
        $issueList = ShopGoodsLogic::getGoodsIssue($goods_where);
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
        return $this->success($outData);
    }

}