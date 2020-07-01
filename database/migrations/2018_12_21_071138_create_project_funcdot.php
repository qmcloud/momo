<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectFuncdot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_funcdot', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->default(0)->unsigned()->comment('所属功能分类id 0代表全部'); // 项目类型id(做的冗余数据 方便查询)
            $table->integer('functype_id')->default(0)->unsigned()->comment('所属功能分类id 0代表全部');          // 所属功能分类id
            $table->integer('model_id')->default(0)->unsigned()->comment('所属功能模块id 0代表全部');         // 所属功能模块id
            $table->string('funcdot_name')->comment('功能点名');             // 功能点名
            $table->text('funcdot_desc')->comment('功能点描述');               // 功能点描述
            $table->integer('bottom_time')->comment('最低周期(h)');             // 最低周期(h)
            $table->integer('time')->comment('周期(h)');                    // 周期(h)
            $table->decimal('discount_price')->comment('该功能的折扣价格');             // 该功能的基础价格
            $table->integer('price')->comment('市场价格');                    // 花费(￥)
            $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 0禁用 1正常');
            $table->index(['type_id','functype_id']);
            $table->index('model_id');
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
        Schema::drop('project_funcdot');
    }
}
