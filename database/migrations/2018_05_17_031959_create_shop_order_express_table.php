<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopOrderExpressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_order_express', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_id')->unsigned()->default(0)->index('order_id');
			$table->integer('shipper_id')->unsigned()->default(0);
			$table->string('shipper_name', 120)->default('')->comment('物流公司名称');
			$table->string('shipper_code', 60)->default('')->comment('物流公司代码');
			$table->string('logistic_code', 20)->default('')->comment('快递单号');
			$table->string('traces', 2000)->default('')->comment('物流跟踪信息');
			$table->tinyInteger('is_finish')->default(0);
			$table->integer('request_count')->nullable()->default(0)->comment('总查询次数');
			$table->timestamp('request_time')->nullable()->default('1970-01-01 08:00:01')->comment('最近一次向第三方查询物流信息时间');
			$table->timestamp('add_time')->default('1970-01-01 08:00:01')->comment('添加时间');
			$table->timestamp('update_time')->default('1970-01-01 08:00:01')->comment('更新时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_order_express');
	}

}
