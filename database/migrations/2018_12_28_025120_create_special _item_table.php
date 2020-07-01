<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('special_item')) {
            Schema::create('special_item', function (Blueprint $table) {
                $table->increments('id')->comment('专栏条目表ID');
                $table->integer('special_id')->unsign()->index()->default(0)->comment('专栏编号 0代表首页');
                $table->string('item_type', 50)->index()->default('')->comment('项目类型');
                $table->string('item_data', 2000)->default('')->comment('条目内容');
                $table->string('item_title', 80)->nullable(false)->default('')->comment('专栏条目标题');
                $table->string('item_desc', 300)->nullable(false)->default('')->comment('专栏条目描述');
                $table->integer('item_link_special_id')->default(0)->comment('跳转数据');
                $table->tinyInteger('sort')->unsigned()->default(255)->comment('排序');
                $table->tinyInteger('item_status')->default(1)->comment('状态 0禁用 1正常');
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
        Schema::dropIfExists('special_item');
    }
}
