<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_order_goods', function(Blueprint $table)
		{
			$table->increments('id')->comment('订单商品表id');
			$table->integer('order_id')->unsigned()->default(0)->index('order_id')->comment('订单id');
			$table->integer('goods_id')->unsigned()->default(0)->index('goods_id')->comment('商品id');
			$table->string('goods_name', 120)->default('')->comment('商品名称');
			$table->smallInteger('number')->unsigned()->default(1)->comment('购买数量');
			$table->decimal('market_price', 10)->default(0.00)->comment('市场价格');
			$table->decimal('retail_price', 10)->default(0.00)->comment('单价');
			$table->text('goods_specifition_name_value', 65535)->nullable()->comment('规格信息说明');
			$table->tinyInteger('is_real')->default(1)->comment('商品类型 0代表虚拟商品 1代表实体商品');
			$table->string('list_pic_url')->default('')->comment('列表图片');
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
		Schema::drop('shop_order_goods');
	}

}
