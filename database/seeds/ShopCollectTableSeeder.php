<?php

use Illuminate\Database\Seeder;

class ShopCollectTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_collect')->delete();
        
        \DB::table('shop_collect')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 0,
                'value_id' => 1,
                'add_time' => 0,
                'is_attention' => 1,
                'type_id' => 0,
            ),
        ));
        
        
    }
}