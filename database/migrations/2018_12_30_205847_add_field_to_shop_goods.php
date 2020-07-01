<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToShopGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_goods', function (Blueprint $table) {
            $table->tinyInteger('goods_type')->unsigned()->default(1)->comment('商品类型 1管理员新增 2用户生成');
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
        Schema::table('shop_goods', function (Blueprint $table) {
        	$table->dropColumn('goods_type');
        });
    }
}
