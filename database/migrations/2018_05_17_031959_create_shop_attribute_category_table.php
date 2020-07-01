<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopAttributeCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_attribute_category', function(Blueprint $table)
		{
			$table->increments('id')->comment('属性分类表ID');
			$table->string('name', 60)->default('')->comment('属性分类名称');
			$table->tinyInteger('enabled')->unsigned()->default(1)->comment('是否可用');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_attribute_category');
	}

}
