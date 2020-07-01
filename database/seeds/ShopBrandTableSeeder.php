<?php

use Illuminate\Database\Seeder;

class ShopBrandTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_brand')->delete();
        
        \DB::table('shop_brand')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '史庆闯',
                'list_pic_url' => 'images/6eb61ec474226c057d61fcecd01de9c7.png',
                'simple_desc' => '232131',
                'pic_url' => 'images/50bdfda30d161366b51fee4d5745a22e.png',
                'sort_order' => 127,
                'is_show' => 1,
                'floor_price' => '0.00',
                'is_new' => 1,
                'new_pic_url' => 'images/50bdfda30d161366b51fee4d5745a22e.png',
                'new_sort_order' => 127,
                'created_at' => '2018-05-29 09:30:11',
                'updated_at' => '2018-05-29 09:30:11',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => '121',
                'list_pic_url' => '',
                'simple_desc' => '122123',
                'pic_url' => 'images/2b107b927a595d7e5929cacf7e0b9484.png',
                'sort_order' => 255,
                'is_show' => 1,
                'floor_price' => '0.00',
                'is_new' => 0,
                'new_pic_url' => 'images/570185ba0905af9f55fc42904668d732.png',
                'new_sort_order' => 10,
                'created_at' => '2018-05-29 09:48:40',
                'updated_at' => '2018-05-29 09:48:40',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => '小T科技',
                'list_pic_url' => '["images\\/da10cbd5a2ca02d7e298ff4088476478.png","images\\/78231e16422642d4d79ac0e8b4e44981.png"]',
                'simple_desc' => '小T科技',
                'pic_url' => 'images/5b683e6f94f44bf48eb10fbb720d3d67.png',
                'sort_order' => 255,
                'is_show' => 1,
                'floor_price' => '0.00',
                'is_new' => 1,
                'new_pic_url' => 'images/7d1d5398f43d812d3d6865d95367c122.jpg',
                'new_sort_order' => 1,
                'created_at' => '2018-05-29 09:52:57',
                'updated_at' => '2018-05-29 09:52:57',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => '易修产品',
            'list_pic_url' => '["images\\/timg (9).jpg"]',
                'simple_desc' => '易修',
            'pic_url' => 'images/timg (13).jpg',
                'sort_order' => 255,
                'is_show' => 1,
                'floor_price' => '0.00',
                'is_new' => 1,
                'new_pic_url' => 'images/d2c5f3de5943ecf47919e3cf5dca84df.jpg',
                'new_sort_order' => 255,
                'created_at' => '2018-06-16 02:26:59',
                'updated_at' => '2018-06-16 02:26:59',
            ),
        ));
        
        
    }
}