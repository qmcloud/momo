<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopGoodsAttributeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_goods_attribute', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->unsigned()->default(0)->index('goods_id');
			$table->integer('attribute_id')->unsigned()->default(0)->index('attr_id');
			$table->text('value', 65535);
			$table->comment = '商城商品属性表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_goods_attribute');
	}

}
