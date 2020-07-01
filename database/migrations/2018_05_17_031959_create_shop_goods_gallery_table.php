<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopGoodsGalleryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_goods_gallery', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('goods_id')->unsigned()->default(0)->index('goods_id');
			$table->string('img_url')->default('');
			$table->string('img_desc')->default('');
			$table->integer('sort_order')->unsigned()->default(5);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_goods_gallery');
	}

}
