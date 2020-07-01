<?php

use Illuminate\Database\Seeder;

class CarouselTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carousel')->delete();
        
        \DB::table('carousel')->insert(array (
            0 => 
            array (
                'id' => 1,
                'carousel_title' => '轮播测试1',
                'carousel_img' => 'images/0a86572e6f67a1cbc92432e1d7817c0c.jpg',
                'carousel_info' => '轮播测试1',
                'state' => 1,
                'created_at' => '2018-05-09 09:16:24',
                'updated_at' => '2018-09-12 09:33:52',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'carousel_title' => '轮播测试2',
                'carousel_img' => 'images/9a93350061b5154d9f9787e8332ba2c1.jpg',
                'carousel_info' => '轮播测试2',
                'state' => 1,
                'created_at' => '2018-05-10 08:50:05',
                'updated_at' => '2018-09-12 08:13:19',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'carousel_title' => '哈哈哈',
                'carousel_img' => 'images/6adf6eee8e83405c8caae80c42a53ffc.jpg',
                'carousel_info' => '2说弟弟撒',
                'state' => 1,
                'created_at' => '2018-06-15 03:48:46',
                'updated_at' => '2018-06-15 03:48:46',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'carousel_title' => '新增哈哈哈',
                'carousel_img' => 'images/565c97e18f7c69fd766193a7cf6e0341.jpg',
                'carousel_info' => '新增新增',
                'state' => 1,
                'created_at' => '2018-06-15 03:51:18',
                'updated_at' => '2018-06-15 03:51:18',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'carousel_title' => '轮播测试3',
                'carousel_img' => 'images/1d00d00b476c1697f961bc9dbc7d53c7.jpg',
                'carousel_info' => '轮播测试3',
                'state' => 0,
                'created_at' => '2018-06-22 08:48:30',
                'updated_at' => '2018-09-12 08:13:35',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'carousel_title' => '轮播测试4',
                'carousel_img' => 'images/76a9f07349c4eed2cddaec5c1a762d84.jpg',
                'carousel_info' => '轮播测试4',
                'state' => 0,
                'created_at' => '2018-06-22 08:48:48',
                'updated_at' => '2018-09-12 08:13:34',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            6 => 
            array (
                'id' => 7,
                'carousel_title' => '轮播测试5',
                'carousel_img' => 'images/bf2f2bf9a3ca51ef10fc577c6baf5429.jpg',
                'carousel_info' => '轮播测试5',
                'state' => 0,
                'created_at' => '2018-06-22 08:49:08',
                'updated_at' => '2018-09-12 08:13:34',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 0,
            ),
            7 => 
            array (
                'id' => 8,
                'carousel_title' => '1231',
                'carousel_img' => 'images/0d351fa7338a937d396154e052d7fc68.png',
                'carousel_info' => '7878788',
                'state' => 1,
                'created_at' => '2018-09-30 06:29:04',
                'updated_at' => '2018-09-30 06:43:16',
                'carousel_type' => 'special',
                'carousel_type_data' => '9989',
                'spec_id' => 0,
                'spec_item_id' => 4,
            ),
            8 => 
            array (
                'id' => 9,
                'carousel_title' => '323423423',
                'carousel_img' => 'images/tim1g.jpg',
                'carousel_info' => '23423',
                'state' => 1,
                'created_at' => '2018-09-30 09:15:53',
                'updated_at' => '2018-09-30 09:15:53',
                'carousel_type' => '',
                'carousel_type_data' => '',
                'spec_id' => 0,
                'spec_item_id' => 4,
            ),
            9 => 
            array (
                'id' => 10,
                'carousel_title' => '哈哈哈',
                'carousel_img' => 'images/FLAMING MOUNTAIN.JPG',
                'carousel_info' => '1231312',
                'state' => 1,
                'created_at' => '2018-10-02 02:39:55',
                'updated_at' => '2018-10-02 02:39:55',
                'carousel_type' => 'special',
                'carousel_type_data' => '213123',
                'spec_id' => 0,
                'spec_item_id' => 2,
            ),
            10 => 
            array (
                'id' => 11,
                'carousel_title' => '测试1',
                'carousel_img' => 'images/57d7d150N1760ef9f.jpg',
                'carousel_info' => '测试内容1',
                'state' => 1,
                'created_at' => '2018-10-02 02:45:50',
                'updated_at' => '2018-10-08 06:15:53',
                'carousel_type' => 'goods',
                'carousel_type_data' => '2',
                'spec_id' => 0,
                'spec_item_id' => 22,
            ),
            11 => 
            array (
                'id' => 12,
                'carousel_title' => '的撒发生打',
                'carousel_img' => 'images/b33d8e4278f0d862284f5935ac86464d.JPG',
                'carousel_info' => '343423423',
                'state' => 1,
                'created_at' => '2018-10-02 08:49:43',
                'updated_at' => '2018-10-02 08:49:43',
                'carousel_type' => 'goods',
                'carousel_type_data' => 'dfsafdsf',
                'spec_id' => 0,
                'spec_item_id' => 23,
            ),
            12 => 
            array (
                'id' => 13,
                'carousel_title' => '888889',
                'carousel_img' => 'images/葬花阁综合论坛邀请函.jpg',
                'carousel_info' => '建行股份分割',
                'state' => 1,
                'created_at' => '2018-10-02 08:50:52',
                'updated_at' => '2018-10-02 08:50:52',
                'carousel_type' => 'special',
                'carousel_type_data' => '共和国的好地方',
                'spec_id' => 0,
                'spec_item_id' => 24,
            ),
            13 => 
            array (
                'id' => 14,
                'carousel_title' => '广泛豆腐干的回复',
                'carousel_img' => 'images/031e01692e39b38687b40e5f2377ec29.JPG',
                'carousel_info' => '更好的防火规范',
                'state' => 1,
                'created_at' => '2018-10-04 06:17:04',
                'updated_at' => '2018-10-04 06:17:04',
                'carousel_type' => 'goods',
                'carousel_type_data' => '213123',
                'spec_id' => 0,
                'spec_item_id' => 9,
            ),
            14 => 
            array (
                'id' => 15,
                'carousel_title' => '的撒发生打',
                'carousel_img' => 'images/645645654654.jpg',
                'carousel_info' => '1231312',
                'state' => 1,
                'created_at' => '2018-10-04 11:32:58',
                'updated_at' => '2018-10-07 07:18:46',
                'carousel_type' => 'goods',
                'carousel_type_data' => '1',
                'spec_id' => 0,
                'spec_item_id' => 34,
            ),
            15 => 
            array (
                'id' => 16,
                'carousel_title' => '的撒发生打',
                'carousel_img' => 'images/42Q58PIC42U_1024.jpg',
                'carousel_info' => '123123',
                'state' => 1,
                'created_at' => '2018-10-05 15:45:24',
                'updated_at' => '2018-10-05 15:45:24',
                'carousel_type' => 'special',
                'carousel_type_data' => '13213',
                'spec_id' => 0,
                'spec_item_id' => 46,
            ),
            16 => 
            array (
                'id' => 17,
                'carousel_title' => '122222222222222222222222222',
                'carousel_img' => 'images/36c84b58fd6b0f9b831a77e8b50463b3.jpg',
                'carousel_info' => '33333333333333333',
                'state' => 1,
                'created_at' => '2018-10-05 15:57:59',
                'updated_at' => '2018-10-05 15:57:59',
                'carousel_type' => 'goods',
                'carousel_type_data' => '222222222222222222222',
                'spec_id' => 0,
                'spec_item_id' => 48,
            ),
            17 => 
            array (
                'id' => 18,
                'carousel_title' => '划拨',
                'carousel_img' => 'images/ebf8285a6ac33defc13df792188a5f4f.jpg',
                'carousel_info' => '建行股份分割',
                'state' => 1,
                'created_at' => '2018-10-06 14:49:08',
                'updated_at' => '2018-10-07 07:18:46',
                'carousel_type' => 'goods',
                'carousel_type_data' => '4',
                'spec_id' => 0,
                'spec_item_id' => 34,
            ),
            18 => 
            array (
                'id' => 19,
                'carousel_title' => '测试2',
                'carousel_img' => 'images/5b7518b8N60e8885d.jpg',
                'carousel_info' => '测试内容2',
                'state' => 1,
                'created_at' => '2018-10-07 06:03:08',
                'updated_at' => '2018-10-08 06:01:55',
                'carousel_type' => 'goods',
                'carousel_type_data' => '3',
                'spec_id' => 0,
                'spec_item_id' => 22,
            ),
            19 => 
            array (
                'id' => 20,
                'carousel_title' => '测试',
                'carousel_img' => 'images/b9a05de9facdc9c1597ef67171ffc097.jpg',
                'carousel_info' => '测试测试测试',
                'state' => 1,
                'created_at' => '2018-10-07 07:18:46',
                'updated_at' => '2018-10-07 07:18:46',
                'carousel_type' => 'goods',
                'carousel_type_data' => '4',
                'spec_id' => 0,
                'spec_item_id' => 34,
            ),
            20 => 
            array (
                'id' => 21,
                'carousel_title' => '测试3',
                'carousel_img' => 'images/08b6067f916f75d0574052a9ebd4e4ea.jpg',
                'carousel_info' => '测试内容3',
                'state' => 1,
                'created_at' => '2018-10-08 06:01:55',
                'updated_at' => '2018-10-08 06:01:55',
                'carousel_type' => 'goods',
                'carousel_type_data' => '4',
                'spec_id' => 0,
                'spec_item_id' => 22,
            ),
        ));
        
        
    }
}