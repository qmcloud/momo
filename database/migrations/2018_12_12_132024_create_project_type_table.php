<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_type', function(Blueprint $table) {
            $table->increments('id');
            $table->string('type_name', 80)->default('')->comment('项目类型名');
            $table->text('type_desc')->nullable()->comment('项目类型描述	');
            $table->string('type_img', 100)->default('')->comment('类型的图片');
            $table->integer('class_id')->default(0)->comment('技术领域类型');
            $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 0禁用 1正常');
            $table->decimal('basal_price', 10, 2)->default(0)->comment('该类项目的基础价格');
            $table->index('class_id');
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
        Schema::drop('project_type');
    }
}
