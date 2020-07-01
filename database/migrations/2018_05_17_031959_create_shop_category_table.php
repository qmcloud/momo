<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_category', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 90)->default('')->comment('分类名称');
			$table->string('keywords')->default('')->comment('关键词');
			$table->string('front_desc')->default('')->comment('贴心描述');
			$table->integer('parent_id')->unsigned()->default(0)->index('parent_id')->comment('父级分类id');
			$table->tinyInteger('sort_order')->unsigned()->default(50)->comment('分类排序');
			$table->tinyInteger('show_index')->default(0)->comment('展示索引');
			$table->tinyInteger('is_show')->default(1)->comment('是否展示');
			$table->string('banner_url')->default('')->comment('banner图片url');
			$table->string('icon_url')->comment('分类图标');
			$table->string('img_url')->nullable()->comment('图片地址');
			$table->string('wap_banner_url')->default('')->comment('baner图片');
			$table->tinyInteger('level')->default(0)->comment('分类层级');
			$table->integer('type')->default(0)->comment('类别');
			$table->string('front_name')->default('')->comment('分类别名');
			$table->comment = '商城商品分类表';
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
		Schema::drop('shop_category');
	}

}
