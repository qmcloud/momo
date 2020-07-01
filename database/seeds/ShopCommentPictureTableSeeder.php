<?php

use Illuminate\Database\Seeder;

class ShopCommentPictureTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shop_comment_picture')->delete();
        
        \DB::table('shop_comment_picture')->insert(array (
            0 => 
            array (
                'id' => 1,
                'comment_id' => 1,
                'pic_url' => 'images/c82d319abd7a28d87f43b7af149750f3.png',
                'sort_order' => 255,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}