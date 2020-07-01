<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopBrandTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_brand', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->default('')->comment('品牌名称');
			$table->string('list_pic_url')->default('')->comment('品牌列表展示图片');
			$table->string('simple_desc')->default('')->comment('品牌描述');
			$table->string('pic_url')->default('')->comment('品牌图片');
			$table->tinyInteger('sort_order')->unsigned()->default(50)->comment('展示排序');
			$table->tinyInteger('is_show')->default(1)->index('is_show')->comment('品牌上下架 1：显示 0：下架');
			$table->decimal('floor_price', 10)->default(0.00)->comment('品牌显示的最低价');
			$table->boolean('is_new')->default(0)->comment('是否是新增品牌');
			$table->string('new_pic_url')->default('')->comment('新增展示图片');
			$table->tinyInteger('new_sort_order')->unsigned()->default(10)->comment('新增逻辑下的排序');
			$table->timestamps();
			$table->comment = '商城品牌表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_brand');
	}

}
