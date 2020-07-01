<?php

use Illuminate\Database\Seeder;

class ShopSpecificationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_specification')->delete();
        
        \DB::table('shop_specification')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '电商系统',
                'sort_order' => 255,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '颜色',
                'sort_order' => 255,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '系统',
                'sort_order' => 255,
            ),
        ));
        
        
    }
}