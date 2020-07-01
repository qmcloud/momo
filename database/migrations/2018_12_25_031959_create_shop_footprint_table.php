<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopFootprintTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_footprint', function(Blueprint $table)
		{
			$table->increments('id')->comment('足迹表id');
			$table->integer('uid')->unsigned()->default(0)->index('uid')->comment('用户uid');
			$table->integer('goods_id')->unsigned()->default(0)->comment('商品id');
			$table->timestamp('add_time')->default('1970-01-01 08:00:01')->comment('浏览时间');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_footprint');
	}

}
