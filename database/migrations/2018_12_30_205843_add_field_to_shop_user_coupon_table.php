<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToShopUserCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_user_coupon', function (Blueprint $table) {
            $table->timestamp('start_time')->default('1970-01-01 08:00:01')->comment('有效期结束时间');
            $table->timestamp('end_time')->default('1970-01-01 08:00:01')->comment('有效期结束时间');
            $table->string('ext0',100)->default('')->comment('扩展字段');
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
        Schema::table('shop_user_coupon', function (Blueprint $table) {
        	$table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('ext0');
        });
    }
}
