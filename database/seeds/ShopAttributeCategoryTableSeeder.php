<?php

use Illuminate\Database\Seeder;

class ShopAttributeCategoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_attribute_category')->delete();
        
        \DB::table('shop_attribute_category')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '电商系统',
                'enabled' => 1,
            ),
        ));
        
        
    }
}