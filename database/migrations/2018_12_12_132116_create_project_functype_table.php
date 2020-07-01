<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectFunctypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_functype', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->default(0)->comment('所属项目类型id 0代表全部');
            $table->string('functype_name', 80)->default('')->comment('功能分类名 ');
            $table->text('functype_desc')->nullable()->comment('功能分类描述 ');
            $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 0禁用 1正常');
            $table->index('type_id');
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
        Schema::drop('project_functype');
    }
}
