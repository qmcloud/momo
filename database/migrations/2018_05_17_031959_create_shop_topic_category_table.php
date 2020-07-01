<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopTopicCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_topic_category', function(Blueprint $table)
		{
			$table->increments('id')->comment('主题专题分类表id');
			$table->string('title')->default('')->comment('主题专题分类标题');
			$table->string('pic_url')->default('')->comment('主题专题分类图片');
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
		Schema::drop('shop_topic_category');
	}

}
