<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('version')) {
            Schema::create('version', function (Blueprint $table) {
                $table->increments('id')->comment('版本表id');
                $table->string('version',150)->default(0)->comment('版本号');
                $table->string('seed', 255)->index()->default('')->comment('seed数据');
                $table->string('handle_data', 300)->nullable(false)->default('')->comment('其他处理数据');
                $table->string('desc', 300)->nullable(false)->default('')->comment('版本说明');
                $table->tinyInteger('status')->default(1)->comment('状态 0可更新 1完成更新');
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
        Schema::dropIfExists('version');
    }
}
