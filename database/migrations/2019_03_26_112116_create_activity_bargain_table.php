<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityBargainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_bargain', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('goods_id')->default(0)->comment('商品ID');
            $table->string('goods_name', 120)->default('')->comment('商品名称');
            $table->string('title', 120)->default('')->comment('砍价活动名称');
            $table->string('info', 255)->default('')->comment('砍价活动简介');
            $table->text('description')->nullable()->comment('砍价详情/说明');
            $table->string('images', 1000)->default('')->comment('砍价产品轮播图');
            $table->timestamp('start_time')->default('1970-01-01 08:00:01')->comment('砍价开启时间');
            $table->timestamp('stop_time')->default('1970-01-01 08:00:01')->comment('砍价结束时间');
            $table->integer('total_num')->default(0)->comment('总库存');
            $table->integer('sales')->default(0)->comment('销量');
            $table->integer('limit_num')->default(1)->comment('每次购买的砍价产品数量');
            $table->decimal('min_price', 10)->default(0.00)->comment('活动价[砍价商品最低价]');
            $table->decimal('bargain_max_price', 10)->default(0.00)->comment('用户每次砍价的最大金额');
            $table->decimal('bargain_min_price', 10)->default(0.00)->comment('用户每次砍价的最小金额');
            $table->integer('help_num')->default(0)->comment('可助力人数');
            $table->integer('look')->default(0)->comment('砍价产品浏览量');
            $table->integer('share')->default(0)->comment('砍价产品分享量');
            $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('砍价状态 0(到砍价时间不自动开启) 1(到砍价时间自动开启时间)');
            $table->text('rule')->nullable()->comment('砍价规则');
            $table->unique('goods_id');
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
        Schema::drop('activity_bargain');
    }
}
