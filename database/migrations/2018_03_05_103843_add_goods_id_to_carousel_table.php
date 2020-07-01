<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsIdToCarouselTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('carousel', 'carousel_link')) {
            Schema::table('carousel', function (Blueprint $table) {
                $table->dropColumn('carousel_link');
                $table->integer('goods_id')->default(0)->comment('商品ID');
            });
        }
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
        if (Schema::hasColumn('carousel', 'goods_id')) {
            Schema::table('carousel', function (Blueprint $table) {
                $table->dropColumn('goods_id');
                $table->string('carousel_link', 200)->nullable(false)->default('')->comment('轮播链接');
            });
        }
    }
}
