<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopShipperTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_shipper', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 20)->default('')->comment('快递公司名称');
			$table->string('code', 10)->default('')->comment('快递公司代码');
			$table->integer('sort_order')->default(10)->comment('排序');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_shipper');
	}

}
