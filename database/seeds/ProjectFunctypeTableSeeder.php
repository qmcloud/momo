<?php

use Illuminate\Database\Seeder;

class ProjectFunctypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('project_functype')->delete();
        
        \DB::table('project_functype')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type_id' => 0,
                'functype_name' => '社交功能',
                'functype_desc' => '包括留言、文字及语音聊天、视频聊天等功能',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-05-07 11:26:37',
                'updated_at' => '2018-07-12 02:16:49',
            ),
            1 => 
            array (
                'id' => 2,
                'type_id' => 1,
                'functype_name' => '基础功能',
                'functype_desc' => '基础功能建设',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-05-07 12:27:44',
                'updated_at' => '2018-05-11 06:44:48',
            ),
            2 => 
            array (
                'id' => 3,
                'type_id' => 0,
                'functype_name' => '高级功能',
                'functype_desc' => '包括市场常见如高级搜索等高级类功能',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:18:56',
                'updated_at' => '2018-07-12 02:18:56',
            ),
            3 => 
            array (
                'id' => 4,
                'type_id' => 0,
                'functype_name' => '电商功能',
                'functype_desc' => '主要包括购物、积分、营销工具等功能',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:19:42',
                'updated_at' => '2018-07-12 02:19:42',
            ),
            4 => 
            array (
                'id' => 5,
                'type_id' => 0,
                'functype_name' => '其他功能',
                'functype_desc' => '主要是一些需求定制类的服务，如UI设计等',
                'sort' => 255,
                'status' => 1,
                'created_at' => '2018-07-12 02:20:41',
                'updated_at' => '2018-07-12 02:20:41',
            ),
        ));
        
        
    }
}