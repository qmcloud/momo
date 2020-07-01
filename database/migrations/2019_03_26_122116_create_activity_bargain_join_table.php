<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityBargainJoinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_bargain_join', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->integer('bargain_id')->default(0)->comment('砍价产品id');
            $table->integer('goods_id')->default(0)->comment('商品ID');
            $table->integer('product_id')->default(0)->comment('商品规格id');
            $table->decimal('bargain_price_min', 10)->default(0.00)->comment('砍价的最低价');
            $table->decimal('bargain_price', 10)->default(0.00)->comment('砍价后的金额');
            $table->decimal('price', 10)->default(0.00)->comment('砍掉的价格');
            $table->integer('help_num')->default(0)->comment('已助力次数');
            $table->tinyInteger('is_addorder')->unsigned()->default(0)->comment('是否下单(0:未下单，1已下单)');
            $table->tinyInteger('status')->default(1)->comment('状态 1参与中 2 活动结束参与失败 3活动结束参与成功');
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
        Schema::drop('activity_bargain_join');
    }
}
