<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 50)->default('')->comment('错误日志记录类型 如order');
            $table->string('node', 300)->default('')->comment('错误节点');
            $table->string('ip', 30)->default('')->comment('错误ip节点');
            $table->string('info', 300)->default('')->comment('错误日志记录说明');
            $table->text('msg')->comment('错误内容');
            $table->text('remark')->nullable()->comment('其他备注');
            $table->tinyInteger('error_level')->default(1)->comment('错误等级');
            $table->tinyInteger('result')->default(1)->comment('错误结果0 错误解决1错误');
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
        Schema::drop('errors_log');
    }
}
