<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToShopCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_comment', function (Blueprint $table) {
            $table->tinyInteger('star')->unsigned()->default(5)->comment('评论星级');
            $table->integer('product_id')->unsigned()->default(0)->comment('商品规格id');
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
        Schema::table('shop_comment', function (Blueprint $table) {
            $table->dropColumn('star');
            $table->dropColumn('product_id');
        });
    }
}
