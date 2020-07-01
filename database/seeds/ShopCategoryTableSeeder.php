<?php

use Illuminate\Database\Seeder;

class ShopCategoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_category')->delete();
        
        \DB::table('shop_category')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '技术开发',
                'keywords' => '1212',
                'front_desc' => '21121',
                'parent_id' => 0,
                'sort_order' => 1,
                'show_index' => 0,
                'is_show' => 1,
            'banner_url' => 'images/timg (8).jpg',
                'icon_url' => 'images/ico-add-addr.png',
                'img_url' => 'images/b948390920521b777113776a3f0aab07.jpg',
                'wap_banner_url' => '',
                'level' => 0,
                'type' => 0,
                'front_name' => '技术改变世界',
                'created_at' => '2018-05-29 08:17:04',
                'updated_at' => '2018-06-22 07:51:51',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'web系统',
                'keywords' => 'web系统及相关开发',
                'front_desc' => 'web相关的系统，包含微信公众开发，网站开发等',
                'parent_id' => 1,
                'sort_order' => 0,
                'show_index' => 0,
                'is_show' => 1,
                'banner_url' => 'images/b5c85b3275d13abd030830531ca1fa31.jpg',
                'icon_url' => 'images/66afeabe3ebf49504a43db8706145046.png',
                'img_url' => 'images/web.png',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => 'web系统及相关开发',
                'created_at' => '2018-05-29 08:28:27',
                'updated_at' => '2018-06-22 07:52:11',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '小程序',
                'keywords' => '哈哈哈哈',
                'front_desc' => '微信小程序系统、微信小程序开发',
                'parent_id' => 1,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
                'banner_url' => 'images/u=85293311,808189361&fm=27&gp=0.jpg',
                'icon_url' => 'images/d298e92c3cb675a553af8029a26eedea.png',
                'img_url' => 'images/3.png',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => '小程序系统',
                'created_at' => '2018-06-01 06:50:23',
                'updated_at' => '2018-07-11 10:19:41',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '精选生鲜',
                'keywords' => '生鲜',
                'front_desc' => '精选生鲜',
                'parent_id' => 0,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
                'banner_url' => 'images/timg.jpg',
                'icon_url' => 'images/5ad87bf0N66c5db7c.png',
            'img_url' => 'images/timg (1).jpg',
                'wap_banner_url' => '',
                'level' => 0,
                'type' => 0,
                'front_name' => '精挑细选_新鲜直达',
                'created_at' => '2018-06-15 10:58:44',
                'updated_at' => '2018-06-15 10:58:44',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '水果',
                'keywords' => '水果',
                'front_desc' => '水果',
                'parent_id' => 4,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
                'banner_url' => 'images/c096fd9f2a8d5f5b0ef5efa94bd87dbd.jpg',
                'icon_url' => 'images/be03467a5a28b3eeb6511c1996b54f81.png',
                'img_url' => 'images/b23e752c12ebfc232991a90aa2bc0055.jpg',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => '水果',
                'created_at' => '2018-06-15 10:59:29',
                'updated_at' => '2018-06-15 10:59:29',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '熟食',
                'keywords' => '熟食',
                'front_desc' => '熟食',
                'parent_id' => 4,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
                'banner_url' => 'images/ffca28172d8ee2cc397ba419577dfa38.jpg',
                'icon_url' => 'images/7.jpg',
                'img_url' => 'images/b4d92f33d83b5227f47ad789a7bdd2b6.jpg',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => '熟食',
                'created_at' => '2018-06-16 02:36:33',
                'updated_at' => '2018-06-16 02:36:33',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '运维服务',
                'keywords' => '服务器升级及日常运维',
                'front_desc' => '服务器升级及日常运维',
                'parent_id' => 1,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
                'banner_url' => 'images/ChMkJljZxEqIGDjxAAJjQwPlkCMAAbHRAE-xqYAAmNb414.jpg',
                'icon_url' => 'images/yunwei.png',
                'img_url' => 'images/c00583305d37d84002ae3167d3378324.png',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => '服务器升级及日常运维',
                'created_at' => '2018-06-22 07:57:28',
                'updated_at' => '2018-06-22 07:57:28',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => '微信公众号',
                'keywords' => '微信公众号开发和系统售卖',
                'front_desc' => '微信公众号开发和系统售卖',
                'parent_id' => 1,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
            'banner_url' => 'images/timg (11).jpg',
                'icon_url' => 'images/gongzhonghao.png',
                'img_url' => 'images/eb33dae22c76d70fb606faf648360817.png',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => '微信公众号开发和系统售卖',
                'created_at' => '2018-06-22 08:02:23',
                'updated_at' => '2018-06-22 08:02:23',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'APP',
                'keywords' => 'APP系统的开发',
                'front_desc' => 'APP系统的开发',
                'parent_id' => 1,
                'sort_order' => 127,
                'show_index' => 0,
                'is_show' => 1,
            'banner_url' => 'images/timg (12).jpg',
                'icon_url' => 'images/apple.png',
                'img_url' => 'images/2b61ccb2d9d368c7c167c630319d77bb.png',
                'wap_banner_url' => '',
                'level' => 1,
                'type' => 0,
                'front_name' => 'APP系统的开发',
                'created_at' => '2018-06-22 08:05:07',
                'updated_at' => '2018-06-22 08:05:07',
            ),
        ));
        
        
    }
}