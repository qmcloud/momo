<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWechatOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('appid', 32);
            $table->string('mch_id', 32)->comment('商户号');
            $table->string('device_info', 32)->nullable();
            $table->string('body', 128)->comment('商品描述');
            $table->string('detail', 6000)->nullable()->comment('商品详情');
            $table->string('attach', 32)->nullable()->comment('附加数据');
            $table->string('out_trade_no', 32)->comment('商户订单号');
            $table->string('fee_type', 16)->default('CNY')->comment('标价币种');
            $table->unsignedInteger('total_fee')->comment('标价金额');
            $table->string('goods_tag', 32)->nullable()->comment('订单优惠标记');
            $table->string('product_id', 32)->nullable()->comment('商品ID');
            $table->string('openid', 128)->nullable()->comment('用户标识');
            $table->timestamp('paid_at')->nullable()->comment('支付时间');
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
        Schema::dropIfExists('wechat_orders');
    }
}
