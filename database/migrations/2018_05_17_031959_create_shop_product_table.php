<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_product', function(Blueprint $table)
		{
			$table->increments('id')->comment('多规格商品id');
			$table->integer('goods_id')->unsigned()->default(0)->comment('基础商品id');
			$table->string('goods_specification_ids', 50)->default('')->comment('所含规格');
			$table->string('goods_sn', 60)->default('')->comment('商品sn');
			$table->integer('goods_number')->unsigned()->default(0)->comment('库存');
			$table->decimal('retail_price', 10)->unsigned()->default(0.00)->comment('单价');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_product');
	}

}
