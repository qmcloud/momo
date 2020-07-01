<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityBargainHelpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_bargain_help', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->integer('bargain_uid')->default(0)->comment('用户参与砍价表uid');
            $table->integer('join_id')->default(0)->comment('参与表id');
            $table->integer('bargain_id')->default(0)->comment('砍价产品id');
            $table->decimal('price', 10)->default(0.00)->comment('砍掉的价格');
            $table->index('bargain_uid');
            $table->index('bargain_id');
            $table->index('uid');
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
        Schema::drop('activity_bargain_help');
    }
}
