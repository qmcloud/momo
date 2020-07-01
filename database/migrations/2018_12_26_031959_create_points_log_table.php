<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePointsLogTable extends Migration {

	/**
     * 用户积分变更表
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('points_log', function(Blueprint $table)
		{
			$table->increments('id')->comment('用户积分变更表id');
			$table->integer('uid')->unsigned()->default(0)->index('uid')->comment('用户uid');
            $table->integer('admin_id')->unsigned()->default(0)->comment('管理员id');
			$table->integer('points_value')->default(0)->comment('积分数负数表示扣除');
            $table->tinyInteger('type')->default(1)->comment('积分变换类型 1 购买商品 2分享商品 ...');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->string('desc', 6550)->comment('备注/操作描述');
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
		Schema::drop('points_log');
	}

}
