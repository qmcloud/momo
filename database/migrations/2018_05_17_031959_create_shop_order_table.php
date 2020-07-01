<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_order', function(Blueprint $table)
		{
			$table->increments('id')->comment('优惠券id');
			$table->string('order_sn', 30)->default('')->unique('order_sn')->comment('优惠券id');
			$table->string('trade_no', 60)->default('')->comment('支付宝微信交易号,用于退款');
			$table->integer('uid')->unsigned()->default(0)->index('user_id')->comment('用户ID');
			$table->tinyInteger('order_status')->default(0)->index('order_status')->comment('订单状态,默认是0-订单关闭或无效，用户取消也置 10-待支付 22-支付完成 32-确认发货 40-订单完成');
			$table->tinyInteger('shipping_status')->default(0)->index('shipping_status')->comment('物流状态 0 带发货 10已发货 20已收货');
			$table->tinyInteger('pay_status')->default(0)->index('pay_status')->comment('支付状态0-异常订单 1-已下单 2-支付校验通过');
			$table->string('consignee', 60)->default('')->comment('收件人');
			$table->integer('country')->unsigned()->default(1)->comment('国家id');
			$table->integer('province')->unsigned()->default(0)->comment('省市id');
			$table->integer('city')->unsigned()->default(0)->comment('区县id');
			$table->integer('district')->unsigned()->default(0)->comment('街道id');
			$table->string('address')->default('')->comment('详细地址');
			$table->string('mobile', 60)->default('')->comment('手机号');
			$table->string('postscript')->default('')->comment('附言');
			$table->decimal('shipping_fee', 10)->default(0.00)->comment('运费');
			$table->string('pay_name', 120)->default('')->comment('支付方式名称');
			$table->tinyInteger('pay_id')->default(0)->index('pay_id')->comment('支付方式id');
			$table->decimal('actual_price', 10)->default(0.00)->comment('实际需要支付的金额');
			$table->integer('integral')->unsigned()->default(0)->comment('使用积分量');
			$table->decimal('integral_money', 10)->default(0.00)->comment('使用的积分量对应的金额');
			$table->decimal('order_price', 10)->default(0.00)->comment('订单总价');
			$table->decimal('goods_price', 10)->default(0.00)->comment('商品总价');
			$table->timestamp('add_time')->default('1970-01-01 08:00:01')->comment('订单生成时间');
			$table->timestamp('confirm_time')->default('1970-01-01 08:00:01')->comment('下单确认时间');
			$table->integer('pay_time')->unsigned()->default(0)->comment('支付时间');
			$table->integer('freight_price')->unsigned()->default(0)->comment('配送费用');
			$table->integer('coupon_id')->unsigned()->default(0)->comment('使用的优惠券id');
			$table->integer('parent_id')->unsigned()->default(0)->comment('代理人id');
			$table->decimal('coupon_price', 10)->default(0.00)->comment('优惠金额');
			$table->tinyInteger('callback_status')->default('0')->comment('支付回调状态');
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
		Schema::drop('shop_order');
	}

}
