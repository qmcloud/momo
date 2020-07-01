<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopAttributeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_attribute', function(Blueprint $table)
		{
			$table->increments('id')->comment('属性ID');
			$table->integer('attribute_category_id')->unsigned()->default(0)->index('cat_id')->comment('属性分类ID');
			$table->string('name', 60)->default('')->comment('属性名称');
			$table->tinyInteger('input_type')->unsigned()->default(1)->comment('属性类别 1代表商品');
			$table->text('values', 65535)->nullable();
			$table->tinyInteger('sort_order')->unsigned()->default(0)->comment('排序');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_attribute');
	}

}
