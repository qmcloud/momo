<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopFeedbackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_feedback', function(Blueprint $table)
		{
			$table->increments('id')->comment('反馈表id');
			$table->integer('uid')->unsigned()->default(0)->index('uid')->comment('用户uid');
			$table->string('user_name', 60)->default('')->comment('用户昵称');
			$table->string('user_contact', 60)->default('')->comment('反馈用户联系方式');
			$table->string('msg_title', 200)->default('')->comment('反馈标题');
			$table->tinyInteger('msg_type')->default(0)->comment('反馈类型');
			$table->tinyInteger('msg_status')->default(1)->comment('反馈状态 0 失效 1正常 2采纳');
			$table->string('msg_content', 600)->comment('反馈内容');
			$table->string('message_img',200)->default('')->comment('反馈附加图片');
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
		Schema::drop('shop_feedback');
	}

}
