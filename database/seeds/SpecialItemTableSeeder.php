<?php

use Illuminate\Database\Seeder;

class SpecialItemTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('special_item')->delete();
        
        \DB::table('special_item')->insert(array (
            0 => 
            array (
                'id' => 16,
                'special_id' => 0,
                'item_type' => 'moduleB',
                'item_data' => '17,18,19,2,3,4,5,6',
                'item_title' => '1312',
                'item_desc' => '1231231',
                'item_link_special_id' => 0,
                'sort' => 0,
                'item_status' => 1,
                'created_at' => '2018-10-01 12:37:05',
                'updated_at' => '2018-10-01 15:14:47',
            ),
            1 => 
            array (
                'id' => 22,
                'special_id' => 0,
                'item_type' => 'adv',
                'item_data' => '',
                'item_title' => '答案发',
                'item_desc' => '的撒发生地',
                'item_link_special_id' => 0,
                'sort' => 255,
                'item_status' => 1,
                'created_at' => '2018-10-02 02:45:49',
                'updated_at' => '2018-10-04 11:32:17',
            ),
            2 => 
            array (
                'id' => 27,
                'special_id' => 0,
                'item_type' => 'home2',
                'item_data' => '',
                'item_title' => '',
                'item_desc' => '',
                'item_link_special_id' => 0,
                'sort' => 65,
                'item_status' => 1,
                'created_at' => '2018-10-03 07:59:00',
                'updated_at' => '2018-10-03 07:59:00',
            ),
            3 => 
            array (
                'id' => 34,
                'special_id' => 0,
                'item_type' => 'moduleC',
                'item_data' => '',
                'item_title' => '哈哈哈哈',
                'item_desc' => '哈哈哈哈哈的倒萨范德萨',
                'item_link_special_id' => 0,
                'sort' => 1,
                'item_status' => 1,
                'created_at' => '2018-10-04 11:32:13',
                'updated_at' => '2018-10-05 03:33:15',
            ),
            4 => 
            array (
                'id' => 38,
                'special_id' => 0,
                'item_type' => 'moduleE',
                'item_data' => '1,4,6',
                'item_title' => '商品横向布局',
                'item_desc' => '商品横向布局商品横向布局',
                'item_link_special_id' => 0,
                'sort' => 1,
                'item_status' => 1,
                'created_at' => '2018-10-05 02:53:06',
                'updated_at' => '2018-10-05 03:33:15',
            ),
            5 => 
            array (
                'id' => 40,
                'special_id' => 0,
                'item_type' => 'moduleB',
                'item_data' => '15,17,2,7',
                'item_title' => '新品推荐',
                'item_desc' => '哈哈哈哈',
                'item_link_special_id' => 0,
                'sort' => 4,
                'item_status' => 1,
                'created_at' => '2018-10-05 03:27:45',
                'updated_at' => '2018-10-05 06:04:14',
            ),
            6 => 
            array (
                'id' => 42,
                'special_id' => 0,
                'item_type' => 'moduleF',
                'item_data' => '5,6,7,8',
                'item_title' => '限时抢购',
                'item_desc' => '限时抢购',
                'item_link_special_id' => 0,
                'sort' => 255,
                'item_status' => 1,
                'created_at' => '2018-10-05 05:53:34',
                'updated_at' => '2018-10-05 08:13:50',
            ),
            7 => 
            array (
                'id' => 46,
                'special_id' => 2,
                'item_type' => 'adv',
                'item_data' => '',
                'item_title' => '11111111111111222',
                'item_desc' => '111111111111111',
                'item_link_special_id' => 0,
                'sort' => 255,
                'item_status' => 1,
                'created_at' => '2018-10-05 15:32:54',
                'updated_at' => '2018-10-05 15:36:03',
            ),
            8 => 
            array (
                'id' => 47,
                'special_id' => 2,
                'item_type' => 'moduleB',
                'item_data' => '1,13,14',
                'item_title' => 'sssss',
                'item_desc' => 'ssssss',
                'item_link_special_id' => 0,
                'sort' => 1,
                'item_status' => 1,
                'created_at' => '2018-10-05 15:51:19',
                'updated_at' => '2018-10-05 15:51:32',
            ),
            9 => 
            array (
                'id' => 48,
                'special_id' => 2,
                'item_type' => 'moduleC',
                'item_data' => '',
                'item_title' => '限时抢1111',
                'item_desc' => '限时抢购',
                'item_link_special_id' => 0,
                'sort' => 1,
                'item_status' => 1,
                'created_at' => '2018-10-05 15:51:39',
                'updated_at' => '2018-10-05 15:53:23',
            ),
        ));
        
        
    }
}