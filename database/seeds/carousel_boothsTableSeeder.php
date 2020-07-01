<?php

use Illuminate\Database\Seeder;

class carousel_boothsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('carousel_booth1')->insert([
            'booth_type'=>'1',
            'carousel_title'=>str_random(30),
            'carousel_info'=>str_random(20),
            'carousel_img'=>'../images/sqc'.mt_rand(1,3).'.jpg',
            'carousel_link'=>'../index.html',
            'state'=>1,
        ]);
    }
}
