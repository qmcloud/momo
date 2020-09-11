<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWechatCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id');
            $table->string('card_type')->comment('类型');
            $table->string('card_id')->comment('微信卡券id');
            $table->string('logo_url', 128)->comment('卡券的商户logo');
            $table->string('code_type', 16)->comment('码型');
            $table->string('brand_name')->comment('商户名字');
            $table->string('title')->comment('卡券名');
            $table->string('color', 16)->comment('券颜色');
            $table->string('notice')->comment('卡券使用提醒');
            $table->string('description')->comment('卡券使用说明');
            $table->json('sku')->comment('商品信息');
            $table->json('date_info')->comment('使用日期');
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
        Schema::dropIfExists('wechat_cards');
    }
}
