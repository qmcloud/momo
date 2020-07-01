<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Logic\ShopGoodsLogic;
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
        if ($request->keyword) {
            $where[] = ['goods_name', 'like', '%' . $request->keyword . '%'];
        }
        if ($request->categoryId) {
            $where['category_id'] = $request->categoryId;
        }
        // 新品
        if ($request->isNew) {
            $where['is_new'] = $request->isNew;
        }
        // 热门
        if ($request->isHot) {
            $where['is_hot'] = $request->isHot;
        }
        // 品牌
        if ($request->brandId) {
            $where['brand_id'] = $request->brandId;
        }
        $order = '';
        $inputSort = $request->input('sort', 'default');
        switch ($inputSort) {
            case 'price':
                $order = 'retail_price ' . $request->input('order', 'asc');
                break;
            default:
                $order = 'sort_order asc';
        }
        $outData = ShopGoodsLogic::getGoodsList($where, $request->size ? $request->size : 10, $order);
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

        $outData = ShopGoodsLogic::getFullGoodsInfo($request->id);
        return $this->success($outData);
    }


    // 商品详情页的关联商品（大家都在看）
    public function getGoodsRelated(Request $request)
    {
        $goodsInfo = ShopGoodsLogic::getGoodsDetail(['id' => $request->id]);
        $relateWhere['category_id'] = $goodsInfo->category_id;
        $outData = ShopGoodsLogic::getRelatedGoods($relateWhere);
        return $this->success($outData);
    }

    // 新品
    public function getGoodsNew(Request $request)
    {
        $outData['bannerInfo'] = Carousel::getCarouselByType(Carousel::BOOTH_TYPE_NEW);
        // 目前只是查询二级分类 因为前段定位分类只有2级
        $outData['filterCategory'] = ShopCategory::getCategoryList(['level' => 1]);
        return $this->success($outData);
    }

    // 热门
    public function getGoodsHot(Request $request)
    {
        $outData['bannerInfo'] = Carousel::getCarouselByType(Carousel::BOOTH_TYPE_HOT);
        // 目前只是查询二级分类 因为前段定位分类只有2级
        $outData['filterCategory'] = ShopCategory::getCategoryList(['level' => 1]);
        return $this->success($outData);
    }

}