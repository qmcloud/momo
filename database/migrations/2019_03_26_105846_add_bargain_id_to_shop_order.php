<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBargainIdToShopOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_order', function (Blueprint $table) {
            $table->integer('bargain_id')->unsigned()->default(0)->comment('砍价id');
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
        Schema::table('shop_order', function (Blueprint $table) {
        	$table->dropColumn('bargain_id');
        });
    }
}
