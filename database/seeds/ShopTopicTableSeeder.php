<?php

use Illuminate\Database\Seeder;

class ShopTopicTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_topic')->delete();
        
        \DB::table('shop_topic')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '专题专题1',
                'content' => '<p></p><p></p><p></p><p></p><p></p><h3><img src="http://img.ui.cn/data/file/6/4/9/688946.jpg?imageMogr2/auto-orient/format/jpg/strip/quality/90/" style="max-width:100%;"><br></h3><p><br></p>',
                'avatar' => 'images/425e64b4058e4eac54416a9cd8f945df.jpg',
                'item_pic_url' => '["images\\/48b2a2aa5320b59e070bc65667c647fc.jpg","images\\/06ea18b81fab6e1fd8218730d0e0ce46.jpg"]',
                'subtitle' => '专题专题1专题专题1专题专题1',
                'topic_category_id' => 0,
                'price_info' => '11.00',
                'read_count' => '0',
                'scene_pic_url' => 'images/5bebbcef9a6aa7a4fe01dbe13e55f23f.jpg',
                'sort_order' => 255,
                'is_show' => 1,
                'created_at' => '2018-05-31 05:55:54',
                'updated_at' => '2018-06-30 05:50:45',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '新增测试专题',
                'content' => '<p></p><p></p><p><img src="http://img.ui.cn/data/file/3/4/8/304843.jpg?imageMogr2/auto-orient/format/jpg/strip/thumbnail/!1800%3E/quality/90/"><br></p><p><img src="http://img.ui.cn/data/file/7/3/8/304837.jpg?imageMogr2/auto-orient/format/jpg/strip/quality/90/"></p><p>&nbsp;&nbsp;<br></p>',
                'avatar' => 'images/1fcb3ed37b3836bfa5f39855919506e9.jpg',
                'item_pic_url' => '["images\\/cb932c69501fa4ca8dbaa0dd21fef0c1.jpg"]',
                'subtitle' => '新增测试专题哈哈哈哈哈',
                'topic_category_id' => 0,
                'price_info' => '0.00',
                'read_count' => '0',
                'scene_pic_url' => 'images/f83d1277d8bd56a61e908bb9218851e3.jpg',
                'sort_order' => 255,
                'is_show' => 1,
                'created_at' => '2018-05-31 07:30:55',
                'updated_at' => '2018-06-30 05:54:00',
            ),
        ));
        
        
    }
}