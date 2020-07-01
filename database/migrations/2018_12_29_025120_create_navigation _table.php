<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('navigation')) {
            Schema::create('navigation', function (Blueprint $table) {
                $table->increments('id')->comment('导航表ID');
                $table->string('icon',150)->default(0)->comment('图标');
                $table->string('nav_title', 100)->index()->default('')->comment('导航标题');
                $table->string('link_type')->default('')->comment('链接类型');
                $table->string('link_data', 300)->nullable(false)->default('')->comment('链接数据');
                $table->string('desc', 300)->nullable(false)->default('')->comment('导航说明');
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
        Schema::dropIfExists('navigation');
    }
}
