<?php

use Illuminate\Database\Seeder;

class missionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mission')->insert([
        	'type_id'=>'1',
        	'mission_title'=>str_random(16),
        	'description'=>str_random(20),
        	'Technology_labels'=>'1,2,3',
        	'budget_amount'=>1000,
        	'deadline'=>1475050602,
        	'end_time'=>1475050602,
        	'maintain_time'=>1475050602,
            'contacts' => str_random(10),
            'phone' => str_random(11),
            'email' => str_random(10).'@gmail.com',
            'remarks' => str_random(10),
            'service_address' => str_random(10),
        ]);
    }
}
