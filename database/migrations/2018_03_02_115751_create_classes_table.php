<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('class_name', 100)->nullable(false)->default('')->comment('分类名称');
                $table->integer('class_pid')->nullable(false)->default(0)->comment('父分类ID');
                $table->tinyInteger('class_level')->unsigned()->nullable(false)->default(0)->comment('所属分类层级');
                $table->string('class_path', 100)->nullable(false)->default('')->comment('分类的全路径');
                $table->string('class_desc', 255)->nullable()->default('')->comment('分类描述');
                $table->tinyInteger('class_state')->unsigned()->nullable(false)->default(1)->comment('分类状态(0-禁用，1-正常)');
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
        Schema::dropIfExists('classes');
    }
}
