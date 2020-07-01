<?php

namespace App\Admin\Controllers\Stat;

use Encore\Admin\Admin;
use App\Models\ShopGoods;
use App\Logic\ShopGoodsLogic;
use App\Models\ShopCategory;

class DoStat
{

    /**
     * 根据商品分类统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function goodsStatByClass()
    {
        $shopCatory = ShopCategory::getParentCategory();
        $shopCatory = array_column($shopCatory->toArray(), null, 'id');
        $shopChildrenCatory = ShopCategory::getCategoryList([['level', '>',0]]);
        $parentData =[];
        $data= [];
        foreach ($shopChildrenCatory as $itemCatory){
            $tmpValue = ShopGoodsLogic::getGoodsCount(['category_id'=>$itemCatory['id']]);
            $data[] = [
                'value'=>$tmpValue,
                'name'=>$itemCatory['name']
            ];
            if(!isset($shopCatory[$itemCatory['parent_id']]['value'])){
                $shopCatory[$itemCatory['parent_id']]['value'] = $tmpValue;
            }else{
                $shopCatory[$itemCatory['parent_id']]['value'] += $tmpValue;
            }
        }
        $cate =  array_pluck($data, 'name');
        $parentData = array_values($shopCatory);
        return view('admin.charts.pie_goods1', compact('data','parentData','cate'));
    }

    /**
     * 根据专题进行统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function goodsStatBySpec()
    {

        $cate = ['新品','人气'];
        // 相关数据
        $data = [
            [
                'value'=> ShopGoodsLogic::getGoodsCount(['is_new'=>ShopGoods::STATE_SALE_NEW]),
                'name'=>'新品'
            ],
            [
                'value'=> ShopGoodsLogic::getGoodsCount(['is_hot'=>ShopGoods::STATE_SALE_RECOMMEND]),
                'name'=>'人气'
            ]
        ];

        return view('admin.charts.pie_goods2', compact('data','cate'));
    }

}
