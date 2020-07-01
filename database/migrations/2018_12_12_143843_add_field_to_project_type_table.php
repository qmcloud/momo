<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToProjectTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('project_type', function (Blueprint $table) {
            $table->string('carousel_imgs',500)->default('')->comment('轮播图片');
            $table->text('description')->nullable()->comment('详情描述');
            $table->tinyInteger('salenum')->default(10)->comment('下单量');
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
            $table->dropColumn('carousel_imgs');
            $table->dropColumn('description');
            $table->dropColumn('salenum');
        });

    }
}
