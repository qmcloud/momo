<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopAddressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_address', function(Blueprint $table)
		{
			$table->increments('id')->comment('收货地址表id');
			$table->string('user_name', 50)->default('')->comment('收货人名称');
			$table->integer('uid')->unsigned()->default(0)->index('uid')->comment('用户id');
			$table->integer('country_id')->default(0)->comment('国家id');
			$table->string('country',50)->default('')->comment('国家');

			$table->integer('province_id')->default(0)->comment('省市id');
			$table->string('province',50)->default('')->comment('省市');

			$table->integer('city_id')->default(0)->comment('区县id');
			$table->string('city',50)->default('')->comment('区县/城市');


			$table->integer('district_id')->default(0)->comment('街道id');
			$table->string('district',50)->default('')->comment('街道');

			$table->string('address', 120)->default('')->comment('详细地址');
			$table->string('mobile', 60)->default('')->comment('手机号');
			$table->tinyInteger('is_default')->default(0)->comment('是否默认');
			$table->tinyInteger('status')->default(1)->comment('状态 1无效 2有效');
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
		Schema::drop('shop_address');
	}

}
