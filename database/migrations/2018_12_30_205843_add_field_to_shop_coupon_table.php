<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToShopCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_coupon', function (Blueprint $table) {
        	$table->tinyInteger('expire_type')->unsigned()->default(0)->comment('到期类型：1=领取后N天过期，2=指定有效期');
        	$table->integer('expire_day')->unsigned()->default(0)->comment('有效天数，expire_type=1时');
        	$table->string('icon',100)->default('')->comment('优惠券icon');
            $table->integer('reward_num')->unsigned()->default(5)->comment('已发出奖品总数');
            $table->integer('total_num')->unsigned()->default(5)->comment('总数量');
            $table->integer('limit_num')->unsigned()->default(5)->comment('限制单个人领取个数');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('优惠券类型 1满减 2折扣');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('优惠券状态');
            $table->string('brief',300)->default('')->comment('简述');
            $table->string('desc',300)->default('')->comment('描述');
            $table->string('ext0',100)->default('')->comment('扩展字段');
            $table->smallInteger('sort_order')->unsigned()->default(100)->comment('排序');
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('shop_coupon', function (Blueprint $table) {
        	$table->dropColumn('expire_type');
            $table->dropColumn('expire_day');
            $table->dropColumn('brief');
            $table->dropColumn('desc');
            $table->dropColumn('type');
        	$table->dropColumn('icon');
            $table->dropColumn('limit_num');
            $table->dropColumn('status');
            $table->dropColumn('ext0');
            $table->dropColumn('sort_order');

        });
    }
}
