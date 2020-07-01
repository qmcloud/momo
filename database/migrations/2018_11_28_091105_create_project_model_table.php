<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_model', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('functype_id')->default(0)->unsigned()->comment('所属功能分类id 0代表全部');          // 所属功能分类id
            $table->integer('type_id')->default(0)->unsigned()->comment('所属功能分类id 0代表全部'); // 项目类型id(做的冗余数据 方便查询)
            $table->string('model_name')->default('')->comment('模块名');   // 模块名
            $table->string('model_desc')->default('')->comment('模块描述');   // 模块描述
            $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 0禁用 1正常');
            $table->index(['type_id','functype_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_model');
    }
}
