<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopUserCouponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_user_coupon', function(Blueprint $table)
		{
			$table->increments('id')->comment('用户优惠表id');
			$table->integer('coupon_id')->default(0)->comment('优惠券id');
			$table->string('coupon_number', 20)->default('')->comment('优惠劵数量');
			$table->integer('uid')->unsigned()->default(0)->index('uid')->comment('用户uid');
			$table->timestamp('used_time')->default('1970-01-01 08:00:01')->comment('最后使用时间');
			$table->timestamp('reward_time')->default('1970-01-01 08:00:01')->comment('优惠领取时间');
			$table->tinyInteger('use_status')->unsigned()->default(10)->comment('状态 10 可以使用 20已使用');
			$table->integer('order_id')->unsigned()->default(0)->comment('订单id');
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
		Schema::drop('shop_user_coupon');
	}

}
