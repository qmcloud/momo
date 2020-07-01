<?php

use Illuminate\Database\Seeder;

class ProjectTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('project_type')->delete();
        
        \DB::table('project_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type_name' => 'web开发',
                'type_desc' => 'web开发',
                'type_img' => 'images/0e11fc45f9b1e46e71d46468a358e9f5.jpg',
                'class_id' => 0,
                'sort' => 255,
                'status' => 1,
                'basal_price' => '999.00',
                'created_at' => NULL,
                'updated_at' => '2018-07-12 02:35:12',
                'carousel_imgs' => '["images\\/beebd3335612985d91f7226e5006ec8d.jpg","images\\/d22f4a870fb2ff05a96fa5a57551c1d0.jpg"]',
                'description' => NULL,
                'salenum' => 10,
                'brand_id' => 4,
            ),
            1 => 
            array (
                'id' => 2,
                'type_name' => 'IOS开发',
                'type_desc' => 'IOS开发',
                'type_img' => 'images/4b7bf0d6335f496d4b220604140fd831.jpg',
                'class_id' => 0,
                'sort' => 255,
                'status' => 1,
                'basal_price' => '2999.00',
                'created_at' => '2018-05-10 11:31:01',
                'updated_at' => '2018-07-12 02:35:04',
                'carousel_imgs' => '["images\\/aad131da9bf36ac552d6f5e380023a4a.jpg","images\\/6097ed28d1adb27504d1469b9ac47569.jpg"]',
                'description' => NULL,
                'salenum' => 10,
                'brand_id' => 4,
            ),
        ));
        
        
    }
}