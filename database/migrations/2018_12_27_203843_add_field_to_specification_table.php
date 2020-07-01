<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_specification', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->default(0)->index('category_id')->comment('分类id');
            $table->tinyInteger('search_index')->unsigned()->default(0)->comment('是否需要检索');
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
        Schema::table('shop_specification', function (Blueprint $table) {
            $table->dropColumn('search_index');
            $table->dropColumn('category_id');
        });
    }
}
