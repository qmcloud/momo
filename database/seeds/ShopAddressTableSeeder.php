<?php

use Illuminate\Database\Seeder;

class ShopAddressTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_address')->delete();
        
        \DB::table('shop_address')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_name' => '史庆闯',
                'uid' => 0,
                'country_id' => 1,
                'country' => '中国',
                'province_id' => 2,
                'province' => '北京',
                'city_id' => 37,
                'city' => '北京市',
                'district_id' => 407,
                'district' => '朝阳区',
                'address' => '打卡发放山东矿机阿范德萨发卡仕达',
                'mobile' => '13717674036',
                'is_default' => 0,
                'status' => 2,
                'created_at' => '2018-06-21 02:06:33',
                'updated_at' => '2018-06-21 03:25:27',
            ),
            1 => 
            array (
                'id' => 3,
                'user_name' => '史庆闯',
                'uid' => 3,
                'country_id' => 1,
                'country' => '中国',
                'province_id' => 2,
                'province' => '北京',
                'city_id' => 37,
                'city' => '北京市',
                'district_id' => 407,
                'district' => '朝阳区',
                'address' => '都发到撒多多撒大所',
                'mobile' => '13717674036',
                'is_default' => 1,
                'status' => 2,
                'created_at' => '2018-06-22 02:13:51',
                'updated_at' => '2018-06-22 02:13:51',
            ),
        ));
        
        
    }
}