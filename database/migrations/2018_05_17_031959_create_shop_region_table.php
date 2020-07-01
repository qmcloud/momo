<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopRegionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_region', function(Blueprint $table)
		{
			$table->increments('id', true)->unsigned();
			$table->integer('parent_id')->unsigned()->default(0)->index('parent_id');
			$table->string('name', 120)->default('');
			$table->tinyInteger('type')->default(2)->index('region_type');
			$table->decimal('lng', 10,4)->unsigned()->default(0.0000)->comment('经度');
			$table->decimal('lat', 10,4)->unsigned()->default(0.0000)->comment('维度');
			$table->integer('agency_id')->unsigned()->default(0)->index('agency_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_region');
	}

}
