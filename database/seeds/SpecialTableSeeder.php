<?php

use Illuminate\Database\Seeder;

class SpecialTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('special')->delete();
        
        \DB::table('special')->insert(array (
            0 => 
            array (
                'id' => 2,
                'class_id' => 0,
                'special_title' => '定制服务',
                'link_url' => '/pages/projectType/projectType',
                'special_desc' => '已经开发完成的系统购买',
                'remark' => '已经开发完成的系统购买',
                'sort' => 255,
                'if_show' => 1,
                'created_at' => '2018-05-10 08:50:36',
                'updated_at' => '2018-07-11 09:35:10',
                'icon' => 'images/心.png',
            ),
            1 => 
            array (
                'id' => 3,
                'class_id' => 0,
                'special_title' => '精选生鲜',
                'link_url' => '/pages/catalog/catalog?id=4',
                'special_desc' => '已经开发完成的系统购买',
                'remark' => '已经开发完成的系统购买',
                'sort' => 255,
                'if_show' => 1,
                'created_at' => '2018-06-22 08:11:44',
                'updated_at' => '2018-06-22 08:11:44',
                'icon' => 'images/642eebd7a7d65f1fa02e04ef6cbcc3e2.png',
            ),
            2 => 
            array (
                'id' => 4,
                'class_id' => 0,
                'special_title' => '技术商品',
                'link_url' => '/pages/catalog/catalog',
                'special_desc' => '已经开发完成的系统购买',
                'remark' => '已经开发完成的系统购买',
                'sort' => 255,
                'if_show' => 1,
                'created_at' => '2018-06-22 08:12:20',
                'updated_at' => '2018-06-22 08:12:20',
                'icon' => 'images/kaifa.png',
            ),
            3 => 
            array (
                'id' => 5,
                'class_id' => 0,
                'special_title' => '精选专题',
                'link_url' => '/pages/topic/topic',
                'special_desc' => '已经开发完成的系统购买',
                'remark' => '已经开发完成的系统购买',
                'sort' => 255,
                'if_show' => 1,
                'created_at' => '2018-06-22 08:14:28',
                'updated_at' => '2018-06-22 08:14:28',
                'icon' => 'images/zhuangti.png',
            ),
        ));
        
        
    }
}