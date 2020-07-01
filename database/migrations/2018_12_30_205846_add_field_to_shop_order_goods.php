<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToShopOrderGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_order_goods', function (Blueprint $table) {
            $table->integer('product_id')->unsigned()->default(0)->comment('主sku id');
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
        Schema::table('shop_order_goods', function (Blueprint $table) {
        	$table->dropColumn('product_id');
        });
    }
}
