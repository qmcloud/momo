<?php

use Illuminate\Database\Seeder;

class NavigationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('navigation')->delete();
        
        \DB::table('navigation')->insert(array (
            0 => 
            array (
                'id' => 1,
                'icon' => 'images/xihuan-12.png',
                'nav_title' => '定制服务',
                'link_type' => 'link_url',
                'link_data' => '/pages/projectType/projectType',
                'desc' => '定制服务',
                'remark' => '定制服务',
                'sort' => 255,
                'if_show' => 1,
                'created_at' => '2018-10-05 14:33:18',
                'updated_at' => '2018-10-08 05:00:22',
            ),
            1 => 
            array (
                'id' => 2,
                'icon' => 'images/saoyisao-07.png',
                'nav_title' => '精选生鲜',
                'link_type' => 'link_url',
                'link_data' => '/pages/catalog/catalog?id=4',
                'desc' => '精选生鲜',
                'remark' => '精选生鲜',
                'sort' => 254,
                'if_show' => 1,
                'created_at' => '2018-10-05 14:37:51',
                'updated_at' => '2018-10-08 05:01:58',
            ),
            2 => 
            array (
                'id' => 3,
                'icon' => 'images/shoutidai-10.png',
                'nav_title' => '技术商品',
                'link_type' => 'special',
                'link_data' => '/pages/catalog/catalog',
                'desc' => '技术商品',
                'remark' => '技术商品',
                'sort' => 253,
                'if_show' => 1,
                'created_at' => '2018-10-05 14:38:38',
                'updated_at' => '2018-10-08 05:01:13',
            ),
            3 => 
            array (
                'id' => 4,
                'icon' => 'images/shoucang-09.png',
                'nav_title' => '精选专题',
                'link_type' => 'link_url',
                'link_data' => '/pages/topic/topic',
                'desc' => '精选专题',
                'remark' => '精选专题',
                'sort' => 251,
                'if_show' => 1,
                'created_at' => '2018-10-05 14:39:15',
                'updated_at' => '2018-10-08 05:02:10',
            ),
        ));
        
        
    }
}