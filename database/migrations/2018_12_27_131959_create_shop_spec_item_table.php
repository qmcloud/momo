<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopSpecItemTable extends Migration {

	/**
     * 规格条目表
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_spec_item', function(Blueprint $table)
		{
			$table->increments('id')->comment('规格项表id');
            $table->integer('spec_id')->unsigned()->default(0)->comment('规格id');
            $table->string('item', 80)->comment('规格项');
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
		Schema::drop('shop_spec_item');
	}

}
