<?php

use Illuminate\Database\Seeder;

class AdminMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_menu')->delete();
        
        \DB::table('admin_menu')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Index',
                'icon' => 'fa-bar-chart',
                'uri' => '/',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'parent_id' => 0,
                'order' => 2,
                'title' => '后台基础管理',
                'icon' => 'fa-tasks',
                'uri' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-05-07 08:17:18',
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 2,
                'order' => 3,
                'title' => '后台用户管理',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
                'created_at' => NULL,
                'updated_at' => '2018-05-07 08:16:27',
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => 2,
                'order' => 4,
                'title' => '角色管理',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
                'created_at' => NULL,
                'updated_at' => '2018-05-07 08:16:36',
            ),
            4 => 
            array (
                'id' => 5,
                'parent_id' => 2,
                'order' => 5,
                'title' => '权限管理',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
                'created_at' => NULL,
                'updated_at' => '2018-05-07 08:16:45',
            ),
            5 => 
            array (
                'id' => 6,
                'parent_id' => 2,
                'order' => 6,
                'title' => '菜单管理',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
                'created_at' => NULL,
                'updated_at' => '2018-05-07 08:16:57',
            ),
            6 => 
            array (
                'id' => 7,
                'parent_id' => 2,
                'order' => 7,
                'title' => '操作日志',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
                'created_at' => NULL,
                'updated_at' => '2018-05-07 08:17:06',
            ),
            7 => 
            array (
                'id' => 8,
                'parent_id' => 0,
                'order' => 8,
                'title' => '定制科技项目商品板块',
                'icon' => 'fa-diamond',
                'uri' => NULL,
                'created_at' => '2018-05-07 08:18:24',
                'updated_at' => '2018-07-11 09:31:51',
            ),
            8 => 
            array (
                'id' => 9,
                'parent_id' => 0,
                'order' => 13,
                'title' => '网站管理',
                'icon' => 'fa-archive',
                'uri' => NULL,
                'created_at' => '2018-05-07 08:18:56',
                'updated_at' => '2018-05-09 07:52:01',
            ),
            9 => 
            array (
                'id' => 10,
                'parent_id' => 9,
                'order' => 14,
                'title' => '前端用户管理',
                'icon' => 'fa-user-md',
                'uri' => 'User',
                'created_at' => '2018-05-07 08:20:37',
                'updated_at' => '2018-05-09 07:52:01',
            ),
            10 => 
            array (
                'id' => 11,
                'parent_id' => 8,
                'order' => 9,
                'title' => '项目类型管理',
                'icon' => 'fa-product-hunt',
                'uri' => 'project-type',
                'created_at' => '2018-05-07 08:24:34',
                'updated_at' => '2018-05-09 07:52:01',
            ),
            11 => 
            array (
                'id' => 12,
                'parent_id' => 8,
                'order' => 10,
                'title' => '项目功能分类管理',
                'icon' => 'fa-ellipsis-v',
                'uri' => 'project-func-type',
                'created_at' => '2018-05-07 08:25:47',
                'updated_at' => '2018-05-09 07:52:01',
            ),
            12 => 
            array (
                'id' => 13,
                'parent_id' => 8,
                'order' => 11,
                'title' => '功能模块管理',
                'icon' => 'fa-tachometer',
                'uri' => 'project-model',
                'created_at' => '2018-05-07 08:27:17',
                'updated_at' => '2018-05-09 07:52:01',
            ),
            13 => 
            array (
                'id' => 14,
                'parent_id' => 8,
                'order' => 12,
                'title' => '功能点管理',
                'icon' => 'fa-tint',
                'uri' => 'project-dot',
                'created_at' => '2018-05-07 12:51:35',
                'updated_at' => '2018-05-09 07:52:01',
            ),
            14 => 
            array (
                'id' => 16,
                'parent_id' => 31,
                'order' => 19,
                'title' => '专题管理',
                'icon' => 'fa-th-list',
                'uri' => 'special',
                'created_at' => '2018-05-09 07:51:31',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            15 => 
            array (
                'id' => 17,
                'parent_id' => 9,
                'order' => 15,
                'title' => '分类管理',
                'icon' => 'fa-clone',
                'uri' => 'classes',
                'created_at' => '2018-05-09 07:57:07',
                'updated_at' => '2018-10-07 05:52:23',
            ),
            16 => 
            array (
                'id' => 19,
                'parent_id' => 0,
                'order' => 16,
                'title' => '商城管理',
                'icon' => 'fa-shopping-bag',
                'uri' => NULL,
                'created_at' => '2018-05-29 09:24:10',
                'updated_at' => '2018-10-07 05:52:23',
            ),
            17 => 
            array (
                'id' => 20,
                'parent_id' => 19,
                'order' => 31,
                'title' => '商城商品分类管理',
                'icon' => 'fa-bars',
                'uri' => 'shop-category',
                'created_at' => '2018-05-29 09:24:26',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            18 => 
            array (
                'id' => 21,
                'parent_id' => 19,
                'order' => 30,
                'title' => '品牌管理',
                'icon' => 'fa-umbrella',
                'uri' => 'shop-brand',
                'created_at' => '2018-05-29 09:26:28',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            19 => 
            array (
                'id' => 22,
                'parent_id' => 19,
                'order' => 29,
                'title' => '商城商品管理',
                'icon' => 'fa-google',
                'uri' => 'shop-goods',
                'created_at' => '2018-05-31 01:53:31',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            20 => 
            array (
                'id' => 23,
                'parent_id' => 19,
                'order' => 28,
                'title' => '商城话题&专题管理',
                'icon' => 'fa-compass',
                'uri' => 'shop-topics',
                'created_at' => '2018-05-31 05:48:40',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            21 => 
            array (
                'id' => 24,
                'parent_id' => 30,
                'order' => 27,
                'title' => '商品规格管理',
                'icon' => 'fa-object-ungroup',
                'uri' => 'shop-specification',
                'created_at' => '2018-06-01 08:28:40',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            22 => 
            array (
                'id' => 25,
                'parent_id' => 19,
                'order' => 23,
                'title' => '属性管理',
                'icon' => 'fa-paperclip',
                'uri' => NULL,
                'created_at' => '2018-06-03 02:53:35',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            23 => 
            array (
                'id' => 26,
                'parent_id' => 25,
                'order' => 25,
                'title' => '属性类别管理',
                'icon' => 'fa-certificate',
                'uri' => 'shop-attribute-category',
                'created_at' => '2018-06-03 02:54:11',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            24 => 
            array (
                'id' => 27,
                'parent_id' => 25,
                'order' => 24,
                'title' => '属性条目管理',
                'icon' => 'fa-sitemap',
                'uri' => 'shop-attribute',
                'created_at' => '2018-06-03 02:54:48',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            25 => 
            array (
                'id' => 28,
                'parent_id' => 19,
                'order' => 22,
                'title' => '商城订单管理',
                'icon' => 'fa-rmb',
                'uri' => 'shop-order',
                'created_at' => '2018-07-10 07:22:58',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            26 => 
            array (
                'id' => 29,
                'parent_id' => 19,
                'order' => 21,
                'title' => '用户反馈管理',
                'icon' => 'fa-gittip',
                'uri' => 'shop-feedback',
                'created_at' => '2018-07-16 08:56:20',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            27 => 
            array (
                'id' => 30,
                'parent_id' => 19,
                'order' => 26,
                'title' => '规格管理',
                'icon' => 'fa-barcode',
                'uri' => NULL,
                'created_at' => '2018-09-22 05:23:17',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            28 => 
            array (
                'id' => 31,
                'parent_id' => 19,
                'order' => 18,
                'title' => '专栏管理',
                'icon' => 'fa-bullhorn',
                'uri' => NULL,
                'created_at' => '2018-10-07 05:51:07',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            29 => 
            array (
                'id' => 32,
                'parent_id' => 31,
                'order' => 20,
                'title' => '首页专栏管理',
                'icon' => 'fa-genderless',
                'uri' => '/module',
                'created_at' => '2018-10-07 05:53:14',
                'updated_at' => '2018-10-07 05:56:34',
            ),
            30 => 
            array (
                'id' => 33,
                'parent_id' => 19,
                'order' => 17,
                'title' => '导航圈管理',
                'icon' => 'fa-at',
                'uri' => '/nav',
                'created_at' => '2018-10-07 05:55:19',
                'updated_at' => '2018-10-07 05:56:34',
            ),
        ));
        
        
    }
}