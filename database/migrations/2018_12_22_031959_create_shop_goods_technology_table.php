<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopGoodsTechnologyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_goods_technology', function(Blueprint $table)
		{ 
			$table->increments('id');
			$table->integer('goods_id')->unsigned()->default(0)->index('id_goods')->comment('对应商品表的id');
			$table->integer('uid')->unsigned()->default(0)->index('uid')->comment('用户的uid');
			$table->tinyInteger('is_on_sale')->default(1)->comment('状态 0禁用 1正常');
			$table->comment = '技术商品和商品对应表';
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
		Schema::drop('shop_goods_technology');
	}

}
