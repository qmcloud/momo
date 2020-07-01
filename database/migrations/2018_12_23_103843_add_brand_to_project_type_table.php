<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrandToProjectTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_type', function (Blueprint $table) {
            $table->integer('brand_id')->unsigned()->default(0)->index('brand_id')->comment('品牌id');
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
        Schema::table('project_type', function (Blueprint $table) {
            $table->dropColumn('brand_id');
        });
    }
}
