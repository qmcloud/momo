<?php

use Illuminate\Database\Seeder;

class ShopGoodsIssueTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_goods_issue')->delete();
        
        \DB::table('shop_goods_issue')->insert(array (
            0 => 
            array (
                'id' => 1,
                'goods_id' => '1',
                'question' => '商品咋样',
                'answer' => '很好很好',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}