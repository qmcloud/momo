<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopTopicTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_topic', function(Blueprint $table)
		{
			$table->increments('id')->comment('主题专题表id');
			$table->string('title')->default('')->comment('主题标题');
			$table->longText('content')->nullable()->comment('主题表述');
			$table->string('avatar')->nullable()->default('')->comment('主题图片');
			$table->string('item_pic_url')->nullable()->default('')->comment('元素图片');
			$table->string('subtitle')->default('')->comment('短标题');
			$table->integer('topic_category_id')->unsigned()->default(0)->comment('主题分类id');
			$table->decimal('price_info', 10)->unsigned()->default(0.00)->comment('价格信息');
			$table->string('read_count')->default('0')->comment('阅读量');
			$table->string('scene_pic_url')->default('')->comment('展示图片');
			$table->tinyInteger('sort_order')->unsigned()->default(100)->comment('主题排序');
			$table->boolean('is_show')->default(1)->comment('是否展示');
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
		Schema::drop('shop_topic');
	}

}
