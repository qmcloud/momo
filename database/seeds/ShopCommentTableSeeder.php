<?php

use Illuminate\Database\Seeder;

class ShopCommentTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_comment')->delete();
        
        \DB::table('shop_comment')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type_id' => 0,
                'value_id' => 1,
                'content' => '的萨芬撒范德萨发',
                'add_time' => '2018-08-08 08:00:00',
                'status' => 1,
                'user_id' => 0,
                'new_content' => '',
                'sort_order' => 255,
                'created_at' => '2018-06-14 20:18:59',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type_id' => 0,
                'value_id' => 1,
                'content' => '的萨芬撒范德萨发',
                'add_time' => '2018-08-08 08:00:00',
                'status' => 1,
                'user_id' => 0,
                'new_content' => '',
                'sort_order' => 255,
                'created_at' => '2018-06-14 20:19:02',
                'updated_at' => '2018-06-14 20:19:02',
            ),
            2 => 
            array (
                'id' => 3,
                'type_id' => 0,
                'value_id' => 1,
                'content' => '的萨芬撒范德萨发',
                'add_time' => '2018-08-08 08:00:00',
                'status' => 1,
                'user_id' => 0,
                'new_content' => '',
                'sort_order' => 255,
                'created_at' => '2018-06-14 20:19:06',
                'updated_at' => '2018-06-14 20:19:02',
            ),
        ));
        
        
    }
}