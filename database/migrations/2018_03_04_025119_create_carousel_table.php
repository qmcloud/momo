<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarouselTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('carousel')) {
            Schema::create('carousel', function (Blueprint $table) {
                $table->increments('id')->comment('轮播表ID');
                $table->tinyInteger('booth_type')->unsign()->default(1)->comment('展位类型 1是首页的 其他的待定义');
                $table->string('carousel_title', 100)->index()->default('')->comment('轮播标题');
                $table->string('carousel_img', 300)->nullable(false)->default('')->comment('轮播图片');
                $table->string('carousel_link', 200)->nullable(false)->default('')->comment('轮播链接');
                $table->string('carousel_info', 500)->nullable(false)->default('')->comment('轮播上显示内容');
                $table->tinyInteger('state')->default(1)->comment('轮播状态 0 禁用 1显示');
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
        Schema::dropIfExists('carousel');
    }
}
