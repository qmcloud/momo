<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopSpecificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_specification', function(Blueprint $table)
		{
			$table->increments('id')->comment('规格表ID');
			$table->string('name', 60)->default('')->comment('规格名称');
			$table->tinyInteger('sort_order')->unsigned()->default(50)->comment('规格排序');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_specification');
	}

}
