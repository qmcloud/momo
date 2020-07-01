<?php

use Illuminate\Database\Seeder;

class ShopGoodsAttributeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_goods_attribute')->delete();
        
        \DB::table('shop_goods_attribute')->insert(array (
            0 => 
            array (
                'id' => 1,
                'goods_id' => 1,
                'attribute_id' => 1,
                'value' => '商品管理、分类管理、广告管理、注册登录等',
            ),
            1 => 
            array (
                'id' => 2,
                'goods_id' => 3,
                'attribute_id' => 1,
                'value' => '测试商品',
            ),
            2 => 
            array (
                'id' => 3,
                'goods_id' => 4,
                'attribute_id' => 1,
                'value' => '测试',
            ),
            3 => 
            array (
                'id' => 4,
                'goods_id' => 1,
                'attribute_id' => 2,
                'value' => '搜索功能、专题功能等',
            ),
            4 => 
            array (
                'id' => 5,
                'goods_id' => 1,
                'attribute_id' => 4,
                'value' => '积分成长、积分抵扣等',
            ),
            5 => 
            array (
                'id' => 6,
                'goods_id' => 1,
                'attribute_id' => 3,
                'value' => '商品搜索',
            ),
            6 => 
            array (
                'id' => 7,
                'goods_id' => 1,
                'attribute_id' => 6,
                'value' => '微信支付、支付宝支付等',
            ),
            7 => 
            array (
                'id' => 8,
                'goods_id' => 1,
                'attribute_id' => 5,
                'value' => '优惠券系统、折扣券系统等',
            ),
        ));
        
        
    }
}