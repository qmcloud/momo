<?php

use Illuminate\Database\Seeder;

class ProjectModelTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('project_model')->delete();
        
        \DB::table('project_model')->insert(array (
            0 => 
            array (
                'id' => 4,
                'functype_id' => 2,
                'type_id' => 0,
                'model_name' => '基础功能测试模块',
                'model_desc' => '基础功能测试模块',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-05-11 06:11:19',
                'updated_at' => '2018-05-11 06:11:19',
            ),
            1 => 
            array (
                'id' => 5,
                'functype_id' => 2,
                'type_id' => 0,
                'model_name' => '注册登录',
                'model_desc' => '包括邮箱注册登录、手机号注册登录和密码找回等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:26:35',
                'updated_at' => '2018-07-12 02:26:35',
            ),
            2 => 
            array (
                'id' => 6,
                'functype_id' => 2,
                'type_id' => 0,
                'model_name' => '第三方登录',
                'model_desc' => '微信登录、qq登录、微博登录等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:27:15',
                'updated_at' => '2018-07-12 02:27:15',
            ),
            3 => 
            array (
                'id' => 7,
                'functype_id' => 2,
                'type_id' => 0,
                'model_name' => '用户中心',
                'model_desc' => '会员首页、用户资料修改更新、第三方登录管理、修改密码、消息中心等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:28:30',
                'updated_at' => '2018-07-12 02:28:30',
            ),
            4 => 
            array (
                'id' => 8,
                'functype_id' => 3,
                'type_id' => 0,
                'model_name' => '通知及推送消息',
                'model_desc' => '邮件通知、短信通知',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:29:22',
                'updated_at' => '2018-07-12 02:29:22',
            ),
            5 => 
            array (
                'id' => 9,
                'functype_id' => 3,
                'type_id' => 0,
                'model_name' => '高级搜索',
                'model_desc' => '算法搜索、条件筛选、只能算法推荐',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:30:02',
                'updated_at' => '2018-07-12 02:30:02',
            ),
            6 => 
            array (
                'id' => 10,
                'functype_id' => 4,
                'type_id' => 0,
                'model_name' => '商品相关',
                'model_desc' => '包括商品展示、城市切换等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 03:34:47',
                'updated_at' => '2018-07-12 03:34:47',
            ),
            7 => 
            array (
                'id' => 11,
                'functype_id' => 4,
                'type_id' => 0,
                'model_name' => '订单系统',
                'model_desc' => '包括订单列表、订单详情、订单流程、收货标记等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 03:36:01',
                'updated_at' => '2018-07-12 03:36:01',
            ),
            8 => 
            array (
                'id' => 12,
                'functype_id' => 4,
                'type_id' => 0,
                'model_name' => '支付',
                'model_desc' => '微信支付、支付宝支付、其他类型',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 03:36:47',
                'updated_at' => '2018-07-12 03:36:47',
            ),
            9 => 
            array (
                'id' => 13,
                'functype_id' => 1,
                'type_id' => 0,
                'model_name' => '即时通讯',
                'model_desc' => '包括基本聊天、语音聊天、视频聊天等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 03:38:45',
                'updated_at' => '2018-07-12 03:38:45',
            ),
            10 => 
            array (
                'id' => 14,
                'functype_id' => 5,
                'type_id' => 0,
                'model_name' => 'UI设计/产品设计',
                'model_desc' => '包括普通设计、和高级设计',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 03:39:33',
                'updated_at' => '2018-07-12 03:39:33',
            ),
        ));
        
        
    }
}