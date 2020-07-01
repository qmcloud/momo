<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopCommentPictureTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_comment_picture', function(Blueprint $table)
		{
			$table->increments('id')->comment('评论表图片表id');
			$table->integer('comment_id')->unsigned()->default(0)->comment('评论表id');
			$table->string('pic_url',500)->default('')->comment('图片地址');
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
		Schema::drop('shop_comment_picture');
	}

}
