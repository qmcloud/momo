<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToCarouselTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carousel', function (Blueprint $table) {
            $table->dropColumn('booth_type');
            $table->integer('spec_id')->unsigned()->default(0)->index('id_spec')->comment('展位类型控制字段 可对应专题id');
            $table->integer('spec_item_id')->unsigned()->default(0)->index('id_spec_item')->comment('展位类型控制字段 可对专题itemid');
            $table->string('carousel_type',50)->default('')->comment('操作类型');
            $table->string('carousel_type_data',100)->default('')->comment('操作数据 对应于操作类型');
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
        Schema::table('carousel', function (Blueprint $table) {
            $table->dropColumn('carousel_type');
            $table->dropColumn('carousel_type_data');
        });
    }
}
