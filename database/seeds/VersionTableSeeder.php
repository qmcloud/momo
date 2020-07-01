<?php

use Illuminate\Database\Seeder;

class VersionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('version')->delete();
        
        \DB::table('version')->insert(array (
            0 => 
            array (
                'id' => 1,
                'version' => 'v0.1.2',
                'seed' => 'all',
                'handle_data' => '',
                'desc' => '测试版',
                'status' => 1,
                'created_at' => '2018-07-20 11:05:08',
                'updated_at' => '2018-10-09 11:05:13',
            ),
            1 => 
            array (
                'id' => 2,
                'version' => 'v0.2.1',
                'seed' => 'CarouselTableSeeder,SpecialTableSeeder,SpecialItemTableSeeder,AdminMenuTableSeeder,NavigationTableSeeder',
                'handle_data' => '',
                'desc' => '发布体验版【首页等页面功能和风格升级】',
                'status' => 1,
                'created_at' => '2018-10-09 11:27:42',
                'updated_at' => '2018-10-09 11:27:45',
            ),
            2 => 
            array (
                'id' => 3,
                'version' => 'v0.2.1',
                'seed' => 'AdminMenuOneTableSeeder',
                'handle_data' => '',
                'desc' => '添加优惠券功能',
                'status' => 0,
                'created_at' => '2018-12-21 11:27:42',
                'updated_at' => '2018-12-21 11:27:45',
            ),
        ));
        
        
    }
}