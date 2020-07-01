<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopCollectTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_collect', function(Blueprint $table)
		{
			$table->increments('id')->comment('关注收藏表id');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('用户id');
			$table->integer('value_id')->unsigned()->default(0)->index('goods_id')->comment('收藏对象id');
			$table->integer('add_time')->unsigned()->default(0)->comment('添加时间');
			$table->boolean('is_attention')->default(0)->index('is_attention')->comment('是否是关注');
			$table->integer('type_id')->unsigned()->default(0)->comment('关注类型 0代表商品');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_collect');
	}

}
