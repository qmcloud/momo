<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopGoodsIssueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_goods_issue', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('goods_id', 65535)->nullable();
			$table->string('question')->nullable();
			$table->string('answer', 45)->nullable();
			$table->timestamps();
			$table->comment = '商城问题表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_goods_issue');
	}

}
