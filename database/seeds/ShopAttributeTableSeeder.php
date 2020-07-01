<?php

use Illuminate\Database\Seeder;

class ShopAttributeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_attribute')->delete();
        
        \DB::table('shop_attribute')->insert(array (
            0 => 
            array (
                'id' => 1,
                'attribute_category_id' => 1,
                'name' => '基础功能',
                'input_type' => 1,
                'values' => NULL,
                'sort_order' => 255,
            ),
            1 => 
            array (
                'id' => 2,
                'attribute_category_id' => 1,
                'name' => '高级功能',
                'input_type' => 1,
                'values' => NULL,
                'sort_order' => 255,
            ),
            2 => 
            array (
                'id' => 3,
                'attribute_category_id' => 1,
                'name' => '搜索功能',
                'input_type' => 1,
                'values' => NULL,
                'sort_order' => 255,
            ),
            3 => 
            array (
                'id' => 4,
                'attribute_category_id' => 1,
                'name' => '会员积分',
                'input_type' => 1,
                'values' => NULL,
                'sort_order' => 255,
            ),
            4 => 
            array (
                'id' => 5,
                'attribute_category_id' => 1,
                'name' => '促销系统',
                'input_type' => 1,
                'values' => NULL,
                'sort_order' => 255,
            ),
            5 => 
            array (
                'id' => 6,
                'attribute_category_id' => 1,
                'name' => '支付系统',
                'input_type' => 1,
                'values' => NULL,
                'sort_order' => 255,
            ),
        ));
        
        
    }
}