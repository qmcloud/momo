<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopGoodsSpecificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_goods_specification', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->unsigned()->default(0)->index('goods_id');
			$table->integer('specification_id')->unsigned()->default(0)->index('specification_id');
			$table->string('value', 50)->default('');
			$table->string('pic_url')->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_goods_specification');
	}

}
