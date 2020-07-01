<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_comment', function(Blueprint $table)
		{
			$table->increments('id')->comment('评论表id');
			$table->boolean('type_id')->default(0)->comment('评论类型 0代表商品');
			$table->integer('value_id')->unsigned()->default(0)->index('id_value')->comment('评论对象id');
			$table->string('content', 6550)->comment('储存为base64编码');
			$table->timestamp('add_time')->default('1971-01-01 08:00:01')->comment('添加时间');
			$table->tinyInteger('status')->default(1)->comment('评论状态');
			$table->integer('user_id')->unsigned()->default(0)->comment('用户uid');
			$table->string('new_content', 6550)->default('')->comment('备用');
			$table->tinyInteger('sort_order')->unsigned()->default(255)->comment('排序');
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
		Schema::drop('shop_comment');
	}

}
