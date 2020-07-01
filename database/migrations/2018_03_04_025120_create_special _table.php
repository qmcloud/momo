<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('special')) {
            Schema::create('special', function (Blueprint $table) {
                $table->increments('id')->comment('专栏表ID');
                $table->tinyInteger('class_id')->unsign()->default(0)->comment('对应分类表中的id');
                $table->string('special_title', 100)->index()->default('')->comment('专栏标题');
                $table->string('link_url', 300)->nullable(false)->default('')->comment('链接地址');
                $table->string('special_desc', 300)->nullable(false)->default('')->comment('专栏描述');
                $table->string('remark', 300)->nullable(false)->default('')->comment('备注');
                $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
                $table->tinyInteger('if_show')->default(1)->comment('状态 0禁用 1正常');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special');
    }
}
