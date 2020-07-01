<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopCouponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_coupon', function(Blueprint $table)
		{
			$table->increments('id')->unsigned()->comment('优惠券id');
			$table->string('name', 60)->default('')->comment('优惠券名称');
			$table->decimal('type_money', 10)->default(0.00)->comment('减免金额或折扣');
			$table->tinyInteger('send_type')->default(0)->comment('优惠券发放类型 1用户直领 2系统发放 3兑换 4其他');
			$table->decimal('min_amount', 10)->unsigned()->default(0.00)->comment('优惠使用最小金额');
			$table->decimal('max_amount', 10)->unsigned()->default(0.00)->comment('优惠使用最大金额');
			$table->timestamp('send_start_date')->default('1970-01-01 08:00:01')->comment('优惠券发放开始时间');
			$table->timestamp('send_end_date')->default('1970-01-01 08:00:01')->comment('优惠券发放截止时间');
			$table->timestamp('use_start_date')->default('1970-01-01 08:00:01')->comment('使用开始时间');
			$table->timestamp('use_end_date')->default('1970-01-01 08:00:01')->comment('使用截止时间');
			$table->decimal('min_goods_amount', 10)->unsigned()->default(0.00)->comment('优惠使用商品的最小金额');
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
		Schema::drop('shop_coupon');
	}

}
