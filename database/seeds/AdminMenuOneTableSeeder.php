<?php

use Illuminate\Database\Seeder;

class AdminMenuOneTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_menu')->insert(array (
            0 => 
            array (
                'id' => 34,
                'parent_id' => 19,
                'order' => 0,
                'title' => '优惠券管理',
                'icon' => 'fa-cart-arrow-down',
                'uri' => 'shop-coupon',
                'created_at' => '2018-12-21 08:39:54',
                'updated_at' => '2018-12-21 08:39:54',
            ),

        ));
        
        
    }
}